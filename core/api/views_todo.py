from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import Task, Todolist, Todolistgroup
from django.utils import timezone
import datetime
import uuid
import json

@require_http_methods(["GET"])
def get_all_task(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
        
    group_id = request.GET.get('groupId')
    if not group_id:
        return JsonResponse({'success': False, 'error': 'Thiếu group id'})

    today = timezone.now().date()
    # Update overdue tasks before fetching
    Task.objects.filter(
        group_id=group_id, 
        is_deleted=0, 
        status='pending', 
        deadline__lt=today
    ).update(status='overdue')

    tasks = list(Task.objects.filter(group_id=group_id, is_deleted=0).values())
    
    # Format dates
    for t in tasks:
        if t['deadline']:
            if isinstance(t['deadline'], (datetime.date, datetime.datetime)):
                t['deadline'] = t['deadline'].strftime('%Y-%m-%d')
        if t['created_at']:
            t['created_at'] = t['created_at'].isoformat()
        if t['updated_at']:
            t['updated_at'] = t['updated_at'].isoformat()
            
    return JsonResponse({
        'success': True,
        'tasks': tasks
    })

@csrf_exempt
@require_http_methods(["POST"])
def create_group(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = request.POST
        title = data.get('title', '').strip()
        user_id = request.session['user_id']
        todolist, created = Todolist.objects.get_or_create(
            user_id=user_id,
            defaults={'todolist_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        if not title:
            return JsonResponse({'success': False, 'error': 'Tên group không được để trống'})
        group_id = str(uuid.uuid4())
        group = Todolistgroup.objects.create(
            group_id=group_id,
            todolist=todolist,
            title=title,
            created_at=timezone.now(),
            is_deleted=0
        )
        return JsonResponse({'success': True, 'group': {'group_id': group.group_id, 'title': group.title}})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def update_group(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        group_id = request.POST.get('groupId')
        title = request.POST.get('title', '').strip()
        if not group_id or not title:
            return JsonResponse({'success': False, 'error': 'Missing data'})
        group = Todolistgroup.objects.get(group_id=group_id)
        group.title = title
        group.save()
        return JsonResponse({'success': True})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def delete_group(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        group_id = request.POST.get('groupId')
        if not group_id:
            return JsonResponse({'success': False, 'error': 'Missing id'})
        group = Todolistgroup.objects.get(group_id=group_id)
        group.is_deleted = 1
        group.save()
        return JsonResponse({'success': True})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def create_task(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = request.POST
        title = data.get('title', '').strip()
        group_id = data.get('group_id')
        if not title or not group_id:
            return JsonResponse({'success': False, 'error': 'Title and Group ID are required'})
        task_id = str(uuid.uuid4())
        deadline = data.get('deadline')
        if deadline == '': deadline = None
        task = Task.objects.create(
            task_id=task_id,
            group_id=group_id,
            title=title,
            description=data.get('description', ''),
            priority=data.get('priority', 'medium'),
            status=data.get('status', 'pending'),
            deadline=deadline,
            created_at=timezone.now(),
            updated_at=timezone.now(),
            is_deleted=0
        )
        return JsonResponse({'success': True, 'task': {'task_id': task.task_id, 'title': task.title, 'status': task.status}})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def update_task(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = request.POST
        task_id = data.get('id')
        if not task_id:
            return JsonResponse({'success': False, 'error': 'Missing id'})
        task = Task.objects.get(task_id=task_id)
        task.title = data.get('title', task.title)
        task.description = data.get('description', task.description)
        task.priority = data.get('priority', task.priority)
        task.status = data.get('status', task.status)
        deadline = data.get('deadline')
        if deadline == '': deadline = None
        task.deadline = deadline
        task.group_id = data.get('group_id', task.group_id)
        task.updated_at = timezone.now()
        task.save()
        return JsonResponse({'success': True})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def toggle_status(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = request.POST
        task_id = data.get('id')
        if not task_id:
            return JsonResponse({'success': False, 'error': 'Missing id'})
        task = Task.objects.get(task_id=task_id)
        if task.status == 'completed':
            # Check if it should be overdue
            today = timezone.now().date()
            if task.deadline and task.deadline < today:
                task.status = 'overdue'
            else:
                task.status = 'pending'
        else:
            task.status = 'completed'
            
        task.updated_at = timezone.now()
        task.save()
        return JsonResponse({'success': True, 'task': {'task_id': task.task_id, 'status': task.status, 'title': task.title, 'priority': task.priority, 'deadline': task.deadline.strftime('%Y-%m-%d') if task.deadline else ''}})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@csrf_exempt
@require_http_methods(["POST"])
def delete_task(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        data = request.POST
        task_id = data.get('id')
        if not task_id:
            return JsonResponse({'success': False, 'error': 'Missing id'})
        task = Task.objects.get(task_id=task_id)
        task.is_deleted = 1
        task.save()
        return JsonResponse({'success': True})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

@require_http_methods(["GET"])
def get_task_detail(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    try:
        task_id = request.GET.get('id')
        if not task_id:
            return JsonResponse({'success': False, 'error': 'Missing id'})
        task = Task.objects.filter(task_id=task_id).values().first()
        if not task:
            return JsonResponse({'success': False, 'error': 'Task not found'})
        if task['deadline']:
            if isinstance(task['deadline'], (datetime.date, datetime.datetime)):
                task['deadline'] = task['deadline'].strftime('%Y-%m-%dT%H:%M:%S')
        return JsonResponse({'success': True, 'task': task})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})
