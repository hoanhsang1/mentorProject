from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import User, Pomodorohistory
from django.utils import timezone
from django.db.models import Count, Sum, Q
import datetime
import json

@csrf_exempt
@require_http_methods(["GET", "POST"])
def handle_admin(request):
    if 'user_id' not in request.session or request.session.get('role') != 'admin':
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    if request.method == 'GET':
        action = request.GET.get('action')
    else:
        # For POST, check if it's JSON or form data
        if request.content_type == 'application/json':
            try:
                data = json.loads(request.body)
                action = data.get('action')
            except:
                action = request.POST.get('action')
        else:
            action = request.POST.get('action')

    if action == 'get_stats':
        return get_dashboard_stats(request)
    elif action == 'get_users':
        return get_users(request)
    elif action == 'get_user_detail':
        return get_user_detail(request)
    elif action == 'toggle_user_status':
        return toggle_user_status(request)
    elif action == 'delete_user':
        return delete_user(request)
    elif action == 'reset_password':
        return reset_password(request)
    elif action == 'get_pomodoro_stats':
        return get_pomodoro_stats(request)
    elif action == 'get_daily_activity':
        return get_daily_activity(request)
    elif action == 'get_activity_table':
        return get_activity_table(request)
    
    return JsonResponse({'success': False, 'error': f'Invalid action: {action}'})

def get_dashboard_stats(request):
    try:
        # Total Users
        total_users = User.objects.filter(is_deleted=0).count()
        
        # New Users (last 30 days)
        date_30_days_ago = timezone.now() - datetime.timedelta(days=30)
        new_users = User.objects.filter(created_at__gte=date_30_days_ago, is_deleted=0).count()
        
        # Study sessions (Pomodoro)
        history = Pomodorohistory.objects.filter(status='completed', is_deleted=0)
        total_sessions = history.count()
        total_minutes = history.aggregate(total=Sum('duration_minutes'))['total'] or 0
        
        # Active Users (last 7 days - users who completed a pomodoro session)
        date_7_days_ago = timezone.now() - datetime.timedelta(days=7)
        active_users = Pomodorohistory.objects.filter(
            created_at__gte=date_7_days_ago, 
            status='completed', 
            is_deleted=0
        ).values('pomodoro__user').distinct().count()
        
        # Daily Stats for chart (last 7 days)
        daily_stats = []
        for i in range(6, -1, -1):
            date = timezone.now().date() - datetime.timedelta(days=i)
            day_history = history.filter(
                created_at__date=date
            )
            daily_stats.append({
                'date': date.strftime('%Y-%m-%d'),
                'sessions': day_history.count(),
                'minutes': day_history.aggregate(total=Sum('duration_minutes'))['total'] or 0
            })

        return JsonResponse({
            'success': True,
            'total_users': total_users,
            'new_users_30_days': new_users,
            'total_study_hours': round(total_minutes / 60, 1),
            'total_sessions': total_sessions,
            'active_users': active_users,
            'daily_stats': daily_stats
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_users(request):
    try:
        page = int(request.GET.get('page', 1))
        limit = int(request.GET.get('limit', 10))
        search = request.GET.get('search', '')
        
        offset = (page - 1) * limit
        
        users_query = User.objects.all()
        if search:
            users_query = users_query.filter(
                Q(username__icontains=search) | 
                Q(fullname__icontains=search) | 
                Q(email__icontains=search)
            )
            
        total = users_query.count()
        users = list(users_query.order_by('-created_at')[offset:offset+limit].values(
            'user_id', 'username', 'fullname', 'email', 'role', 'is_deleted', 'created_at'
        ))
        
        # Format dates for JSON
        for user in users:
            if user['created_at']:
                user['created_at'] = user['created_at'].strftime('%Y-%m-%d %H:%M:%S')

        return JsonResponse({
            'success': True,
            'users': users,
            'pagination': {
                'page': page,
                'limit': limit,
                'total': total,
                'total_pages': (total + limit - 1) // limit
            }
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def toggle_user_status(request):
    try:
        if request.content_type == 'application/json':
            data = json.loads(request.body)
            user_id = data.get('user_id')
        else:
            user_id = request.POST.get('user_id')
            
        if not user_id:
            return JsonResponse({'success': False, 'error': 'User ID is required'})
            
        user = User.objects.get(user_id=user_id)
        # 0 = active, 1 = locked/deleted
        user.is_deleted = 0 if user.is_deleted == 1 else 1
        user.updated_at = timezone.now()
        user.save()
        
        status_text = 'locked' if user.is_deleted == 1 else 'unlocked'
        return JsonResponse({
            'success': True,
            'message': f'User {status_text} successfully',
            'new_status': user.is_deleted
        })
    except User.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'User not found'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def delete_user(request):
    try:
        if request.content_type == 'application/json':
            data = json.loads(request.body)
            user_id = data.get('user_id')
        else:
            user_id = request.POST.get('user_id')
            
        if not user_id:
            return JsonResponse({'success': False, 'error': 'User ID is required'})
            
        user = User.objects.get(user_id=user_id)
        # Soft delete
        user.is_deleted = 1
        user.updated_at = timezone.now()
        user.save()
        
        return JsonResponse({
            'success': True,
            'message': 'User deleted successfully'
        })
    except User.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'User not found'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_user_detail(request):
    try:
        user_id = request.GET.get('user_id')
        if not user_id:
            return JsonResponse({'success': False, 'error': 'User ID is required'})
            
        user_obj = User.objects.get(user_id=user_id)
        user_data = {
            'user_id': user_obj.user_id,
            'username': user_obj.username,
            'fullname': user_obj.fullname,
            'email': user_obj.email,
            'role': user_obj.role,
            'created_at': user_obj.created_at.strftime('%Y-%m-%d %H:%M:%S') if user_obj.created_at else None,
            'is_deleted': user_obj.is_deleted
        }
        
        # Get Pomodoro stats for this user
        history = Pomodorohistory.objects.filter(pomodoro__user_id=user_id, status='completed', is_deleted=0)
        pomodoro_stats = {
            'total_sessions': history.count(),
            'total_minutes': history.aggregate(total=Sum('duration_minutes'))['total'] or 0
        }
        
        # Get recent activity
        recent_activity = list(history.order_by('-created_at')[:10].values(
            'history_id', 'study_topic', 'duration_minutes', 'created_at'
        ))
        for act in recent_activity:
            if act['created_at']:
                act['created_at'] = act['created_at'].strftime('%Y-%m-%d %H:%M:%S')

        return JsonResponse({
            'success': True,
            'user': user_data,
            'pomodoro_stats': pomodoro_stats,
            'recent_activity': recent_activity
        })
    except User.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'User not found'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_pomodoro_stats(request):
    try:
        days = int(request.GET.get('days', 30))
        date_from = timezone.now() - datetime.timedelta(days=days)
        
        history = Pomodorohistory.objects.filter(
            created_at__gte=date_from,
            status='completed',
            is_deleted=0
        )
        
        # Aggregate by topic or just return total
        total_sessions = history.count()
        total_minutes = history.aggregate(total=Sum('duration_minutes'))['total'] or 0
        avg_session_length = total_minutes / total_sessions if total_sessions > 0 else 0
        
        stats = {
            'total_sessions': total_sessions,
            'total_minutes': total_minutes,
            'avg_session_length': round(avg_session_length, 1),
            'active_users': history.values('pomodoro__user').distinct().count()
        }
        
        # Top users by study time
        from django.db.models import F
        top_users = list(history.values(username=F('pomodoro__user__username')).annotate(
            total_minutes=Sum('duration_minutes'),
            sessions=Count('history_id')
        ).order_by('-total_minutes')[:10])

        return JsonResponse({
            'success': True,
            'stats': stats,
            'top_users': top_users
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def reset_password(request):
    try:
        if request.content_type == 'application/json':
            data = json.loads(request.body)
            user_id = data.get('user_id')
        else:
            user_id = request.POST.get('user_id')
            
        if not user_id:
            return JsonResponse({'success': False, 'error': 'User ID is required'})
            
        import secrets
        import string
        import bcrypt
        
        # Generate new random password
        new_password = ''.join(secrets.choice(string.ascii_letters + string.digits) for _ in range(8))
        hashed_password = bcrypt.hashpw(new_password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
        
        user = User.objects.get(user_id=user_id)
        user.password = hashed_password
        user.updated_at = timezone.now()
        user.save()
        
        return JsonResponse({
            'success': True,
            'message': 'Password reset successfully',
            'new_password': new_password
        })
    except User.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'User not found'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_daily_activity(request):
    try:
        days = int(request.GET.get('days', 7))
        daily_activity = []
        
        history = Pomodorohistory.objects.filter(status='completed', is_deleted=0)
        
        for i in range(days - 1, -1, -1):
            date = timezone.now().date() - datetime.timedelta(days=i)
            day_history = history.filter(created_at__date=date)
            daily_activity.append({
                'date': date.strftime('%Y-%m-%d'),
                'sessions': day_history.count(),
                'minutes': day_history.aggregate(total=Sum('duration_minutes'))['total'] or 0
            })
            
        return JsonResponse({
            'success': True,
            'daily_activity': daily_activity
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_activity_table(request):
    try:
        days = int(request.GET.get('days', 7))
        page = int(request.GET.get('page', 1))
        limit = int(request.GET.get('limit', 10))
        
        offset = (page - 1) * limit
        date_from = timezone.now() - datetime.timedelta(days=days)
        
        query = Pomodorohistory.objects.filter(
            created_at__gte=date_from,
            is_deleted=0
        )
        
        total = query.count()
        # Join with User to get username
        # Since Pomodorohistory -> Pomodoro -> User
        activities = list(query.select_related('pomodoro__user').order_by('-created_at')[offset:offset+limit].values(
            'history_id', 
            'status', 
            'duration_minutes', 
            'study_topic', 
            'start_time',
            'pomodoro__title', 
            'pomodoro__user__username'
        ))
        
        # Flattening and formatting
        formatted_activities = []
        for act in activities:
            formatted_activities.append({
                'history_id': act['history_id'],
                'status': act['status'],
                'duration_minutes': act['duration_minutes'],
                'study_topic': act['study_topic'],
                'start_time': act['start_time'].strftime('%Y-%m-%d %H:%M:%S') if act['start_time'] else None,
                'pomodoro_title': act['pomodoro__title'],
                'username': act['pomodoro__user__username']
            })

        return JsonResponse({
            'success': True,
            'activity': formatted_activities,
            'pagination': {
                'page': page,
                'limit': limit,
                'total': total,
                'total_pages': (total + limit - 1) // limit
            }
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})
