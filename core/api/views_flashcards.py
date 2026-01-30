from django.http import JsonResponse
from django.views.decorators.http import require_http_methods
from django.views.decorators.csrf import csrf_exempt
from core.models import Flashcard, Flashcardset, Flashcarditem, Flashcardprogress
from django.utils import timezone
import uuid
from django.db.models import Count, Q

@csrf_exempt
@require_http_methods(["GET", "POST"])
def handle_flashcards(request):
    if 'user_id' not in request.session:
        return JsonResponse({'success': False, 'error': 'Unauthorized'})
    
    user_id = request.session['user_id']
    action = request.GET.get('action') or request.POST.get('action')
    
    if action == 'get_stats':
        return get_stats(user_id)
    elif action == 'get_sets':
        return get_sets(user_id)
    elif action == 'create_set':
        return create_set(request, user_id)
    elif action == 'update_set':
        return update_set(request)
    elif action == 'delete_set':
        return delete_set(request)
    elif action == 'get_cards':
        return get_cards(request)
    elif action == 'create_card' or action == 'add_card':
        return create_card(request)
    elif action == 'update_card':
        return update_card(request)
    elif action == 'delete_card':
        return delete_card(request)
    elif action == 'study_cards' or action == 'get_unlearned_cards':
        return study_cards(request, user_id)
    elif action == 'update_progress':
        return update_progress(request, user_id)
    
    return JsonResponse({'success': False, 'error': f'Invalid action {action}'})

def get_stats(user_id):
    try:
        user_flashcard, created = Flashcard.objects.get_or_create(
            user_id=user_id,
            defaults={'flashcard_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        
        sets = Flashcardset.objects.filter(flashcard=user_flashcard, is_deleted=0).annotate(
            total_cards=Count('flashcarditem', filter=Q(flashcarditem__is_deleted=0)),
            learned_cards=Count('flashcarditem__flashcardprogress', 
                               filter=Q(flashcarditem__flashcardprogress__status='learned', 
                                        flashcarditem__flashcardprogress__user_id=user_id))
        )
        
        total_cards = sum(s.total_cards for s in sets)
        learned_cards = sum(s.learned_cards for s in sets)
        total_sets = sets.count()
        progress = round((learned_cards / total_cards * 100), 1) if total_cards > 0 else 0
        
        sets_data = []
        for s in sets:
            sets_data.append({
                'set_id': s.set_id,
                'title': s.title,
                'total_cards': s.total_cards,
                'learned_cards': s.learned_cards,
                'created_at': s.created_at
            })
            
        return JsonResponse({
            'success': True,
            'total_sets': total_sets,
            'total_cards': total_cards,
            'learned_cards': learned_cards,
            'progress': progress,
            'sets': sets_data
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_sets(user_id):
    try:
        user_flashcard, created = Flashcard.objects.get_or_create(
            user_id=user_id,
            defaults={'flashcard_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        sets = list(Flashcardset.objects.filter(flashcard=user_flashcard, is_deleted=0).values())
        return JsonResponse({'success': True, 'sets': sets})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def create_set(request, user_id):
    title = request.POST.get('title', '').strip()
    if not title:
        return JsonResponse({'success': False, 'error': 'Title is required'})
    try:
        user_flashcard, created = Flashcard.objects.get_or_create(
            user_id=user_id,
            defaults={'flashcard_id': str(uuid.uuid4()), 'created_at': timezone.now()}
        )
        set_id = str(uuid.uuid4())
        Flashcardset.objects.create(
            set_id=set_id,
            title=title,
            flashcard=user_flashcard,
            is_deleted=0,
            created_at=timezone.now()
        )
        return JsonResponse({'success': True, 'set_id': set_id, 'title': title, 'message': 'Flashcard set created successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def update_set(request):
    set_id = request.POST.get('set_id')
    title = request.POST.get('title', '').strip()
    if not set_id or not title:
        return JsonResponse({'success': False, 'error': 'Set ID and title are required'})
    try:
        obj = Flashcardset.objects.get(set_id=set_id)
        obj.title = title
        obj.save()
        return JsonResponse({'success': True, 'message': 'Set updated successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def delete_set(request):
    set_id = request.POST.get('set_id')
    if not set_id:
        return JsonResponse({'success': False, 'error': 'Set ID is required'})
    try:
        obj = Flashcardset.objects.get(set_id=set_id)
        obj.is_deleted = 1
        obj.save()
        return JsonResponse({'success': True, 'message': 'Set deleted successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def get_cards(request):
    set_id = request.GET.get('set_id')
    if not set_id:
        return JsonResponse({'success': False, 'error': 'Set ID is required'})
    try:
        cards = list(Flashcarditem.objects.filter(set_id=set_id, is_deleted=0).values())
        return JsonResponse({'success': True, 'cards': cards})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def create_card(request):
    set_id = request.POST.get('set_id')
    question = request.POST.get('question', '').strip()
    answer = request.POST.get('answer', '').strip()
    if not set_id or not question or not answer:
        return JsonResponse({'success': False, 'error': 'All fields are required'})
    try:
        card_id = str(uuid.uuid4())
        Flashcarditem.objects.create(
            card_id=card_id,
            set_id=set_id,
            question=question,
            answer=answer,
            learned=0,
            created_at=timezone.now(),
            updated_at=timezone.now(),
            is_deleted=0
        )
        return JsonResponse({'success': True, 'card_id': card_id, 'message': 'Card created successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def update_card(request):
    card_id = request.POST.get('card_id')
    question = request.POST.get('question', '').strip()
    answer = request.POST.get('answer', '').strip()
    if not card_id or not question or not answer:
        return JsonResponse({'success': False, 'error': 'All fields are required'})
    try:
        obj = Flashcarditem.objects.get(card_id=card_id)
        obj.question = question
        obj.answer = answer
        obj.updated_at = timezone.now()
        obj.save()
        return JsonResponse({'success': True, 'message': 'Card updated successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def delete_card(request):
    card_id = request.POST.get('card_id')
    if not card_id:
        return JsonResponse({'success': False, 'error': 'Card ID is required'})
    try:
        obj = Flashcarditem.objects.get(card_id=card_id)
        obj.is_deleted = 1
        obj.save()
        return JsonResponse({'success': True, 'message': 'Card deleted successfully'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def study_cards(request, user_id):
    set_id = request.GET.get('set_id')
    include_all = request.GET.get('include_all', '0') == '1'
    
    try:
        # Get learned cards from progress table
        learned_card_ids = set(Flashcardprogress.objects.filter(
            user_id=user_id, 
            status='learned'
        ).values_list('card_id', flat=True))
        
        if set_id:
            cards_query = Flashcarditem.objects.filter(set_id=set_id, is_deleted=0)
            if not include_all:
                # Exclude BOTH progress records and direct learned=1 flags
                cards_query = cards_query.exclude(card_id__in=learned_card_ids).filter(learned=0)
            
            cards = list(cards_query.values())
            
            # If empty unlearned session, fall back to all cards to avoid "No cards" message
            if not cards and not include_all:
                cards = list(Flashcarditem.objects.filter(set_id=set_id, is_deleted=0).values())
        else:
            # General review
            cards = list(Flashcarditem.objects.filter(
                is_deleted=0,
                learned=0
            ).exclude(
                card_id__in=learned_card_ids
            ).order_by('?')[:20].values())
            
        # Explicitly set progress_status for the frontend
        for card in cards:
            is_learned = card['card_id'] in learned_card_ids or card['learned'] == 1
            card['progress_status'] = 'learned' if is_learned else 'new'
            # Ensure learned is 1/0 for JS
            card['learned'] = 1 if is_learned else 0
                
        return JsonResponse({
            'success': True, 
            'cards': cards,
            'mode': 'all' if include_all else 'unlearned'
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})

def update_progress(request, user_id):
    card_id = request.POST.get('card_id')
    status = request.POST.get('status', 'reviewed')
    if not card_id:
        return JsonResponse({'success': False, 'error': 'Card ID is required'})
    try:
        # 1. Update/Create Flashcardprogress
        progress, created = Flashcardprogress.objects.get_or_create(
            card_id=card_id,
            user_id=user_id,
            defaults={'progress_id': str(uuid.uuid4()), 'created_at': timezone.now(), 'status': status}
        )
        if not created:
            progress.status = status
            progress.last_reviewed = timezone.now()
            progress.save()
            
        # 2. ALSO update Flashcarditem.learned for redundancy/legacy compatibility
        if status == 'learned':
            Flashcarditem.objects.filter(card_id=card_id).update(learned=1, updated_at=timezone.now())
        else:
            Flashcarditem.objects.filter(card_id=card_id).update(learned=0, updated_at=timezone.now())
            
        return JsonResponse({'success': True, 'message': 'Progress updated'})
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})
