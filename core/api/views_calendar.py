from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import Calendar, Event
from django.utils import timezone
import datetime
import uuid
import json

@csrf_exempt
@require_http_methods(["GET", "POST"])
def handle_calendar(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    if request.method == 'GET':
        return handle_get(request)
    else:
        return handle_post(request)

def handle_get(request):
    action = request.GET.get('action', 'getEvents')
    user_id = request.session['user_id']
    
    if action == 'getEvents':
        start_date = request.GET.get('start', datetime.datetime.now().replace(day=1).strftime('%Y-%m-%d'))
        end_date = request.GET.get('end', (datetime.datetime.now().replace(day=1) + datetime.timedelta(days=32)).replace(day=1).strftime('%Y-%m-%d'))
        
        # Ensure end_date includes the full day if no time specified
        if len(end_date) == 10:  # Format YYYY-MM-DD
            end_date = end_date + ' 23:59:59'
        
        try:
            calendar_obj, created = Calendar.objects.get_or_create(
                user_id=user_id,
                defaults={
                    'calendar_id': str(uuid.uuid4()), 
                    'name': 'Default Calendar',
                    'color': '#3b82f6',
                    'is_deleted': 0,
                    'created_at': timezone.now(),
                    'updated_at': timezone.now()
                }
            )
            
            events = Event.objects.filter(
                calendar=calendar_obj,
                is_deleted=0,
                start_at__gte=start_date,
                start_at__lte=end_date
            )
            
            formatted_events = []
            for e in events:
                formatted_events.append({
                    'event_id': e.event_id,
                    'title': e.title,
                    'description': e.description,
                    'start_at': e.start_at.strftime('%Y-%m-%d %H:%M:%S') if e.start_at else None,
                    'end_at': e.end_at.strftime('%Y-%m-%d %H:%M:%S') if e.end_at else None,
                    'is_all_day': e.is_all_day,
                    'location': e.location,
                    'event_type': e.event_type,
                    'status': e.status,
                    'priority': e.priority,
                    'calendar_color': calendar_obj.color,
                    'calendar_id': calendar_obj.calendar_id
                })
                
            return JsonResponse({
                'success': True,
                'events': formatted_events,
                'calendar': {
                    'calendar_id': calendar_obj.calendar_id,
                    'title': getattr(calendar_obj, 'name', 'Default'),
                    'color': calendar_obj.color
                }
            })
        except Exception as e:
            return JsonResponse({'success': False, 'error': str(e)})
    
    elif action == 'getStats':
        calendar_obj = Calendar.objects.filter(user_id=user_id).first()
        if not calendar_obj:
            return JsonResponse({'success': True, 'total': 0, 'scheduled': 0, 'completed': 0, 'upcoming': 0})
            
        total = Event.objects.filter(calendar=calendar_obj, is_deleted=0).count()
        completed = Event.objects.filter(calendar=calendar_obj, status='completed', is_deleted=0).count()
        scheduled = Event.objects.filter(calendar=calendar_obj, status='scheduled', is_deleted=0).count()
        upcoming = Event.objects.filter(calendar=calendar_obj, start_at__gt=timezone.now(), is_deleted=0).count()
        
        return JsonResponse({
            'success': True,
            'total': total,
            'scheduled': scheduled,
            'completed': completed,
            'upcoming': upcoming
        })
        
    return JsonResponse({'success': False, 'error': 'Invalid action'})

def handle_post(request):
    # Try to get data from POST form or JSON body
    if request.content_type == 'application/json':
        data = json.loads(request.body)
        action = data.get('action', 'createEvent')
    else:
        data = request.POST
        action = data.get('action', 'createEvent')
        
    user_id = request.session['user_id']
    calendar_obj = Calendar.objects.filter(user_id=user_id).first()
    
    if action == 'createEvent':
        title = data.get('title')
        if not title:
            return JsonResponse({'success': False, 'error': 'Title is required'})
            
        if not calendar_obj:
            calendar_obj = Calendar.objects.create(
                calendar_id=str(uuid.uuid4()),
                user_id=user_id,
                name="Default",
                color='#3b82f6',
                is_deleted=0,
                created_at=timezone.now(),
                updated_at=timezone.now()
            )
            
        event_id = str(uuid.uuid4())
        Event.objects.create(
            event_id=event_id,
            calendar=calendar_obj,
            title=title,
            description=data.get('description', ''),
            start_at=data.get('start_at'),
            end_at=data.get('end_at'),
            location=data.get('location', ''),
            event_type=data.get('event_type', 'event'),
            priority=data.get('priority', 'medium'),
            is_all_day=int(data.get('is_all_day', 0)),
            status='scheduled',
            created_at=timezone.now(),
            updated_at=timezone.now(),
            is_deleted=0
        )
        return JsonResponse({'success': True, 'message': 'Event created successfully'})
        
    elif action == 'updateEvent':
        event_id = data.get('id')
        event = Event.objects.filter(event_id=event_id, calendar__user_id=user_id).first()
        if not event:
            return JsonResponse({'success': False, 'error': 'Event not found'})
            
        event.title = data.get('title', event.title)
        event.description = data.get('description', event.description)
        event.start_at = data.get('start_at', event.start_at)
        event.end_at = data.get('end_at', event.end_at)
        event.location = data.get('location', event.location)
        event.event_type = data.get('event_type', event.event_type)
        event.priority = data.get('priority', event.priority)
        event.is_all_day = int(data.get('is_all_day', event.is_all_day))
        event.updated_at = timezone.now()
        event.save()
        return JsonResponse({'success': True, 'message': 'Event updated successfully'})

    elif action == 'completeEvent':
        event_id = data.get('id')
        event = Event.objects.filter(event_id=event_id, calendar__user_id=user_id).first()
        if event:
            event.status = 'completed'
            event.updated_at = timezone.now()
            event.save()
            return JsonResponse({'success': True, 'message': 'Event completed successfully'})
        return JsonResponse({'success': False, 'error': 'Event not found'})

    elif action == 'updateEventStatus':
        event_id = data.get('id')
        status = data.get('status')
        event = Event.objects.filter(event_id=event_id, calendar__user_id=user_id).first()
        if event:
            event.status = status
            event.updated_at = timezone.now()
            event.save()
            return JsonResponse({'success': True, 'message': 'Event status updated successfully'})
        return JsonResponse({'success': False, 'error': 'Event not found'})

    elif action == 'deleteEvent':
        event_id = data.get('id')
        event = Event.objects.filter(event_id=event_id, calendar__user_id=user_id).first()
        if event:
            event.is_deleted = 1
            event.save()
            return JsonResponse({'success': True, 'message': 'Event deleted successfully'})
        return JsonResponse({'success': False, 'error': 'Event not found'})
        
    return JsonResponse({'success': False, 'error': 'Invalid action'})
