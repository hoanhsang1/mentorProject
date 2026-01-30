from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import Pomodoro, Pomodorohistory
from django.utils import timezone
import datetime
import uuid

@require_http_methods(["GET"])
def get_pomodoro(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    try:
        pomodoro = Pomodoro.objects.filter(user_id=user_id).values().first()
        if not pomodoro:
            # Create default pomodoro for user if not exists
            pomodoro_id = str(uuid.uuid4())
            Pomodoro.objects.create(
                pomodoro_id=pomodoro_id,
                user_id=user_id,
                title="Default",
                status="stopped",
                work_duration=25,
                break_duration=5,
                current_session="work",
                sessions_completed=0,
                created_at=timezone.now(),
                updated_at=timezone.now()
            )
            pomodoro = Pomodoro.objects.filter(pomodoro_id=pomodoro_id).values().first()
            
        return JsonResponse({'success': True, 'pomodoro': pomodoro})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def update_settings(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    work_duration = int(request.POST.get('work_duration', 25))
    break_duration = int(request.POST.get('break_duration', 5))
    
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        pomodoro.work_duration = work_duration
        pomodoro.break_duration = break_duration
        pomodoro.updated_at = timezone.now()
        pomodoro.save()
        
        return JsonResponse({'success': True, 'pomodoro': {
            'pomodoro_id': pomodoro.pomodoro_id,
            'work_duration': pomodoro.work_duration,
            'break_duration': pomodoro.break_duration
        }})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@require_http_methods(["GET"])
def get_history(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    limit = int(request.GET.get('limit', 10))
    
    try:
        history = list(Pomodorohistory.objects.filter(
            pomodoro__user_id=user_id, 
            is_deleted=0
        ).order_by('-created_at')[:limit].values())
        
        return JsonResponse({'success': True, 'history': history})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@require_http_methods(["GET"])
def get_stats(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    days = int(request.GET.get('days', 7))
    date_from = timezone.now() - datetime.timedelta(days=days)
    
    try:
        # Simple stats aggregation
        history = Pomodorohistory.objects.filter(
            pomodoro__user_id=user_id,
            created_at__gte=date_from,
            status='completed',
            is_deleted=0
        )
        
        total_sessions = history.count()
        total_minutes = sum(h.duration_minutes for h in history if h.duration_minutes)
        
        return JsonResponse({
            'success': True,
            'stats': [], # For chart if needed
            'summary': {
                'total_sessions': total_sessions,
                'total_minutes': total_minutes,
                'total_hours': round(total_minutes / 60, 1),
                'days_analyzed': days
            }
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def start_session(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    study_topic = request.POST.get('study_topic', '')
    
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        
        # Create history record
        history_id = str(uuid.uuid4())
        history = Pomodorohistory.objects.create(
            history_id=history_id,
            pomodoro=pomodoro,
            start_time=timezone.now(),
            study_topic=study_topic if study_topic else None,
            status='in_progress',
            duration_minutes=0,
            is_deleted=0,
            created_at=timezone.now()
        )
        
        # Update pomodoro state
        pomodoro.status = 'running'
        pomodoro.current_session = 'work'
        pomodoro.updated_at = timezone.now()
        pomodoro.save()
        
        return JsonResponse({
            'success': True, 
            'pomodoro': Pomodoro.objects.filter(pomodoro_id=pomodoro.pomodoro_id).values().first(),
            'session': Pomodorohistory.objects.filter(history_id=history_id).values().first()
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def pause_session(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        pomodoro.status = 'paused'
        pomodoro.updated_at = timezone.now()
        pomodoro.save()
        
        return JsonResponse({'success': True, 'pomodoro': Pomodoro.objects.filter(pomodoro_id=pomodoro.pomodoro_id).values().first()})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def resume_session(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        pomodoro.status = 'running'
        pomodoro.updated_at = timezone.now()
        pomodoro.save()
        
        return JsonResponse({'success': True, 'pomodoro': Pomodoro.objects.filter(pomodoro_id=pomodoro.pomodoro_id).values().first()})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def end_session(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    completed = request.POST.get('completed') == 'true'
    
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        active_session = Pomodorohistory.objects.filter(
            pomodoro=pomodoro, 
            status='in_progress'
        ).first()
        
        if not active_session:
             return JsonResponse({'success': False, 'error': 'No active session'})

        end_time = timezone.now()
        duration = (end_time - active_session.start_time).total_seconds()
        duration_minutes = int(duration // 60)
        
        active_session.end_time = end_time
        active_session.duration_minutes = duration_minutes
        active_session.status = 'completed' if completed else 'stopped'
        active_session.updated_at = end_time
        active_session.save()
        
        if completed and pomodoro.current_session == 'work':
            pomodoro.sessions_completed += 1
            
        pomodoro.status = 'stopped'
        pomodoro.updated_at = end_time
        pomodoro.save()
        
        return JsonResponse({
            'success': True,
            'pomodoro': Pomodoro.objects.filter(pomodoro_id=pomodoro.pomodoro_id).values().first(),
            'session': {
                'duration_minutes': duration_minutes,
                'completed': completed
            }
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def switch_session(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    try:
        pomodoro = Pomodoro.objects.get(user_id=user_id)
        prev_session = pomodoro.current_session
        new_session = 'break' if prev_session == 'work' else 'work'
        
        pomodoro.current_session = new_session
        if prev_session == 'break' and new_session == 'work':
            pomodoro.sessions_completed += 1
            
        pomodoro.updated_at = timezone.now()
        pomodoro.save()
        
        return JsonResponse({
            'success': True,
            'pomodoro': Pomodoro.objects.filter(pomodoro_id=pomodoro.pomodoro_id).values().first(),
            'previous_session': prev_session,
            'new_session': new_session
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})
