from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import Habit, Habitlist, Habitlistlog
from django.utils import timezone
import datetime
import uuid
import json
import calendar

@require_http_methods(["GET"])
def get_habits(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    month = int(request.GET.get('month', datetime.datetime.now().month))
    year = int(request.GET.get('year', datetime.datetime.now().year))
    
    try:
        # Get or create habit container
        habit_container, created = Habit.objects.get_or_create(
            user_id=user_id,
            defaults={'habit_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        
        # Get habit list
        habits = Habitlist.objects.filter(habit=habit_container, is_deleted=0)
        
        habit_data = []
        for h in habits:
            # Get logs for current month
            logs = Habitlistlog.objects.filter(
                habitlist=h,
                date__year=year,
                date__month=month,
                status='completed',
                is_deleted=0
            ).values_list('date', flat=True)
            
            habit_data.append({
                'habitlist_id': h.habitlist_id,
                'name': h.name,
                'color': h.color,
                'daily_target': h.daily_target,
                'completed_days': [d.day for d in logs] # Just day number for JS compatibility
            })
            
        last_day = calendar.monthrange(year, month)[1]
        
        return JsonResponse({
            'success': True,
            'habits': habit_data,
            'current_month': calendar.month_name[month],
            'year': year,
            'month': month,
            'days_in_month': last_day,
            'today': datetime.datetime.now().day
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def create_habit(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    try:
        data = json.loads(request.body)
        title = data.get('title', '').strip()
        color = data.get('color', '#4a6cf7')
        
        if not title:
            return JsonResponse({'success': False, 'error': 'Title is required'})
            
        user_id = request.session['user_id']
        habit_container, created = Habit.objects.get_or_create(
            user_id=user_id,
            defaults={'habit_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        
        habitlist_id = str(uuid.uuid4())
        Habitlist.objects.create(
            habitlist_id=habitlist_id,
            habit=habit_container,
            name=title,
            color=color,
            daily_target=1,
            created_at=timezone.now(),
            is_deleted=0
        )
        
        return JsonResponse({'success': True, 'message': 'Habit created successfully', 'habitlist_id': habitlist_id})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def toggle_habit(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    try:
        data = json.loads(request.body)
        habitlist_id = data.get('habit_id')
        date_str = data.get('date', datetime.datetime.now().strftime('%Y-%m-%d'))
        
        if not habitlist_id:
            return JsonResponse({'success': False, 'error': 'Habit ID is required'})
            
        habit_list_item = Habitlist.objects.get(habitlist_id=habitlist_id)
        if habit_list_item.habit.user_id != request.session['user_id']:
            return JsonResponse({'success': False, 'error': 'Unauthorized'})
            
        date_obj = datetime.datetime.strptime(date_str, '%Y-%m-%d').date()
        log = Habitlistlog.objects.filter(habitlist=habit_list_item, date=date_obj).first()
        
        if log:
            if log.is_deleted == 0:
                log.is_deleted = 1
                status = False
            else:
                log.is_deleted = 0
                status = True
            log.save()
        else:
            Habitlistlog.objects.create(
                log_id=str(uuid.uuid4()),
                habitlist=habit_list_item,
                date=date_obj,
                status='completed',
                created_at=timezone.now(),
                is_deleted=0
            )
            status = True
            
        return JsonResponse({'success': True, 'status': status})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@require_http_methods(["GET"])
def get_habit_stats(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
        
    user_id = request.session['user_id']
    month = int(request.GET.get('month', datetime.datetime.now().month))
    year = int(request.GET.get('year', datetime.datetime.now().year))
    
    try:
        habit_container = Habit.objects.get(user_id=user_id)
        habits = Habitlist.objects.filter(habit=habit_container, is_deleted=0)
        
        total_completions = Habitlistlog.objects.filter(
            habitlist__habit=habit_container,
            date__year=year,
            date__month=month,
            status='completed',
            is_deleted=0
        ).count()
        
        last_day = calendar.monthrange(year, month)[1]
        total_possible = habits.count() * last_day
        completion_rate = round((total_completions / total_possible * 100), 1) if total_possible > 0 else 0
        
        return JsonResponse({
            'success': True,
            'total_habits': habits.count(),
            'total_completions': total_completions,
            'completion_rate': completion_rate,
            'streaks': []
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def update_habit(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = json.loads(request.body)
        habitlist_id = data.get('habit_id')
        title = data.get('title', '').strip()
        color = data.get('color')
        daily_target = data.get('daily_target', 1)
        
        habit = Habitlist.objects.get(habitlist_id=habitlist_id)
        if habit.habit.user_id != request.session['user_id']:
            return JsonResponse({'success': False, 'error': 'Unauthorized'})
            
        habit.name = title
        if color:
            habit.color = color
        habit.daily_target = daily_target
        habit.save()
        return JsonResponse({'success': True, 'message': 'Habit updated successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def delete_habit(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = json.loads(request.body)
        habitlist_id = data.get('habit_id')
        habit = Habitlist.objects.get(habitlist_id=habitlist_id)
        if habit.habit.user_id != request.session['user_id']:
            return JsonResponse({'success': False, 'error': 'Unauthorized'})
            
        habit.is_deleted = 1
        habit.save()
        return JsonResponse({'success': True, 'message': 'Habit deleted successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})
