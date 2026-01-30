from django.shortcuts import render, redirect
from django.contrib import messages
from django.http import HttpResponseRedirect
from django.urls import reverse
from django.db.models import Q
from .models import User, UsersAvatar, Todolist, Todolistgroup, Flashcard, Flashcardset
from .forms import LoginForm, RegisterForm
import bcrypt
import uuid
import datetime
from django.utils import timezone
from django.conf import settings
import os

def populate_user_session(request):
    """Helper to populate user info for templates"""
    if 'user_id' not in request.session:
        return False
    
    user_id = request.session['user_id']
    try:
        user = User.objects.get(user_id=user_id)
        # Mock request.user for template compatibility
        request.user = user 
        # Map fields for header.html
        request.user.username = user.username
        request.user.first_name = user.fullname
        request.user.last_name = ""
        request.user.role = user.role
        
        # Ensure avatar_path is in session
        try:
            avatar = UsersAvatar.objects.get(user_id=user_id)
            path = avatar.path
            if not path.startswith('/static/'):
                if path.startswith('/'):
                    path = '/static' + path
                else:
                    path = '/static/' + path
            request.session['avatar_path'] = path
        except UsersAvatar.DoesNotExist:
            request.session['avatar_path'] = None
        return True
    except User.DoesNotExist:
        if 'user_id' in request.session:
            del request.session['user_id']
        return False

def index(request):
    if not populate_user_session(request):
        return redirect('login')
        
    context = {
        'page_title': 'Dashboard',
        'sidebar_type': 'free',
        'current_page': 'index'
    }
    
    return render(request, 'core/dashboard/index.html', context)

def login_view(request):
    if 'user_id' in request.session:
        return redirect('index')

    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid():
            username_input = form.cleaned_data['username']
            password_input = form.cleaned_data['password']

            # Find user by username or email
            user = User.objects.filter(Q(username=username_input) | Q(email=username_input)).first()

            if user:
                # Check if account is blocked
                if user.is_deleted:
                    form.add_error('username', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.')
                # Check password
                elif user:
                    try:
                        if bcrypt.checkpw(password_input.encode('utf-8'), user.password.encode('utf-8')):
                            # Success
                            request.session['user_id'] = user.user_id
                            request.session['username'] = user.username
                            request.session['fullname'] = user.fullname
                            request.session['role'] = user.role
                            
                            populate_user_session(request)

                            return redirect('index')
                        else:
                            form.add_error('password', 'Mật khẩu không chính xác.')
                    except Exception as e:
                        # Handle legacy hashes or errors
                        form.add_error(None, f'Lỗi đăng nhập: {str(e)}')
            else:
                form.add_error('username', 'Tài khoản không tồn tại.')
    else:
        form = LoginForm()

    return render(request, 'core/auth/login.html', {'form': form}) 

def register_view(request):
    if 'user_id' in request.session:
        return redirect('index')

    if request.method == 'POST':
        form = RegisterForm(request.POST)
        if form.is_valid():
            # Create user
            new_user = User()
            new_user.user_id = str(uuid.uuid4())
            new_user.username = form.cleaned_data['username']
            new_user.fullname = form.cleaned_data['fullname']
            new_user.email = form.cleaned_data['email']
            
            # Hash password
            salt = bcrypt.gensalt()
            hashed = bcrypt.hashpw(form.cleaned_data['password'].encode('utf-8'), salt)
            new_user.password = hashed.decode('utf-8')
            
            new_user.created_at = timezone.now()
            new_user.updated_at = timezone.now()
            new_user.is_deleted = 0
            new_user.role = 'free'
            
            new_user.save()
            
            messages.success(request, 'Đăng ký thành công! Vui lòng đăng nhập.')
            return redirect('login')
    else:
        form = RegisterForm()

    return render(request, 'core/auth/register.html', {'form': form})

def logout_view(request):
    request.session.flush()
    return redirect('login')

# Placeholders for other views
def dashboard(request):
    return index(request)

def calendar(request):
    if not populate_user_session(request):
        return redirect('login')

    # Get optional params
    current_month = request.GET.get('month', datetime.datetime.now().month)
    current_year = request.GET.get('year', datetime.datetime.now().year)
    selected_date = request.GET.get('date', datetime.datetime.now().strftime('%Y-%m-%d'))
    
    # ensure proper type
    try:
        current_month = int(current_month)
        current_year = int(current_year)
    except:
        current_month = datetime.datetime.now().month
        current_year = datetime.datetime.now().year
    
    # Date logic for template (simple version)
    days_of_week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    
    context = {
        'page_title': 'Calendar',
        'show_breadcrumb': True,
        'current_month': current_month,
        'current_year': current_year,
        'selected_date': selected_date,
        'days_of_week': days_of_week,
        'current_date': datetime.date(current_year, current_month, 1),
        'current_page': 'calendar'
    }

    return render(request, 'core/calendar/index.html', context)

def todo(request):
    if not populate_user_session(request):
        return redirect('login')
    
    user_id = request.session['user_id']
    
    try:
        todolist = Todolist.objects.get(user_id=user_id)
        request.session['todolist'] = todolist.todolist_id
    except Todolist.DoesNotExist:
        # Create new Todo list
        todolist = Todolist()
        todolist.todolist_id = str(uuid.uuid4())
        todolist.user_id = user_id
        todolist.created_at = timezone.now()
        todolist.save()
        request.session['todolist'] = todolist.todolist_id

    # Get Groups
    groups = Todolistgroup.objects.filter(todolist_id=todolist.todolist_id, is_deleted=0)
    
    context = {
        'page_title': 'Todo List',
        'show_breadcrumb': True,
        'groups': groups,
        'current_page': 'todo'
    }
    return render(request, 'core/todo/index.html', context)

def profile(request):
    if not populate_user_session(request):
        return redirect('login')
    
    user_id = request.session['user_id']
    try:
        user = User.objects.get(user_id=user_id)
        avatar = UsersAvatar.objects.filter(user_id=user_id).first()
        
        context = {
            'page_title': 'Profile',
            'show_breadcrumb': True,
            'user': user,
            'avatarPath': avatar.path if avatar else None,
            'current_page': 'profile'
        }
        return render(request, 'core/profile/index.html', context)
    except User.DoesNotExist:
        return redirect('logout')

import os
from django.conf import settings

def upload_avatar(request):
    if request.method == 'POST' and request.FILES.get('avatar'):
        if 'user_id' not in request.session:
            return redirect('login')
            
        user_id = request.session['user_id']
        avatar_file = request.FILES['avatar']
        
        # Simple validation
        if avatar_file.size > 2 * 1024 * 1024:
            messages.error(request, "File too large!")
            return redirect('profile')
            
        ext = os.path.splitext(avatar_file.name)[1].lower()
        if ext not in ['.jpg', '.jpeg', '.png', '.gif']:
            messages.error(request, "Invalid format!")
            return redirect('profile')
            
        # Save file
        upload_dir = os.path.join(settings.BASE_DIR, 'core', 'static', 'assets', 'images', 'avatars')
        if not os.path.exists(upload_dir):
            os.makedirs(upload_dir, exist_ok=True)
            
        new_name = f"avatar_{user_id}_{int(timezone.now().timestamp())}{ext}"
        full_path = os.path.join(upload_dir, new_name)
        
        with open(full_path, 'wb+') as destination:
            for chunk in avatar_file.chunks():
                destination.write(chunk)
                
        path_for_db = f"assets/images/avatars/{new_name}"
        
        # Update DB
        avatar_obj, created = UsersAvatar.objects.get_or_create(user_id=user_id)
        
        # Delete old file if exists
        if avatar_obj.path:
            old_path = os.path.join(settings.BASE_DIR, 'core', 'static', avatar_obj.path)
            if os.path.exists(old_path):
                try:
                    os.remove(old_path)
                except:
                    pass
        
        avatar_obj.path = path_for_db
        avatar_obj.save()
        
        request.session['avatar_path'] = '/static/' + path_for_db
        messages.success(request, "Avatar updated successfully!")
        
    return redirect('profile')

def update_profile(request):
    if request.method == 'POST':
        if 'user_id' not in request.session:
            return redirect('login')
            
        user_id = request.session['user_id']
        fullname = request.POST.get('fullname')
        email = request.POST.get('email')
        
        try:
            user = User.objects.get(user_id=user_id)
            user.fullname = fullname
            user.email = email
            user.updated_at = timezone.now()
            user.save()
            
            # Update session
            request.session['fullname'] = fullname
            
            messages.success(request, "Thông tin cá nhân đã được cập nhật!")
        except Exception as e:
            messages.error(request, f"Có lỗi xảy ra: {str(e)}")
            
    return redirect('profile')

def settings_view(request):
    if not populate_user_session(request):
        return redirect('login')
    return render(request, 'core/settings/index.html', {'page_title': 'Settings', 'show_breadcrumb': True, 'current_page': 'settings'})

def pomodoro(request):
    if not populate_user_session(request):
        return redirect('login')
        
    context = {
        'page_title': 'Pomodoro Timer',
        'show_breadcrumb': True,
        'current_page': 'pomodoro'
    }
    return render(request, 'core/pomodoro/index.html', context)

def admin_dashboard(request):
    if not populate_user_session(request):
        return redirect('login')
    
    user_id = request.session['user_id']
    try:
        user = User.objects.get(user_id=user_id)
        if user.role != 'admin':
            return redirect('dashboard')
            
        context = {
            'page_title': 'Admin Dashboard',
            'sidebar_type': 'admin',
            'current_page': 'admin',
        }
        return render(request, 'core/admin/index.html', context)
    except User.DoesNotExist:
        return redirect('logout')

def flashcards(request):
    if not populate_user_session(request):
        return redirect('login')
    
    user_id = request.session['user_id']
    
    # Get or create flashcard container for user
    try:
        user_flashcard = Flashcard.objects.get(user_id=user_id)
    except Flashcard.DoesNotExist:
        user_flashcard = Flashcard.objects.create(
            flashcard_id=str(uuid.uuid4()),
            user_id=user_id,
            created_at=timezone.now()
        )
    
    # Get sets
    sets = Flashcardset.objects.filter(
        flashcard=user_flashcard,
        is_deleted=0
    ).order_by('-created_at')
    
    context = {
        'page_title': 'Flashcards',
        'show_breadcrumb': True,
        'sets': sets,
        'total_sets': sets.count(),
        'current_page': 'flashcards'
    }
    return render(request, 'core/flashcards/index.html', context)

def habit(request):
    if not populate_user_session(request):
        return redirect('login')
    
    context = {
        'page_title': 'Habit Tracker',
        'show_breadcrumb': True,
        'current_page': 'habit'
    }
    return render(request, 'core/habit/index.html', context)
