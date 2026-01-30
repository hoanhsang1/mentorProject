from django.urls import path
from . import views
from core.api import views_todo, views_pomodoro, views_flashcards, views_habit, views_calendar, views_admin

urlpatterns = [
    path('', views.index, name='index'),
    path('dashboard/', views.index, name='dashboard'),
    path('login/', views.login_view, name='login'),
    path('register/', views.register_view, name='register'),
    path('logout/', views.logout_view, name='logout'),
    path('calendar/', views.calendar, name='calendar'),
    path('todo/', views.todo, name='todo'),
    path('profile/', views.profile, name='profile'),
    path('profile/upload', views.upload_avatar, name='profile_upload'),
    path('profile/update', views.update_profile, name='profile_update'),
    path('settings/', views.settings_view, name='settings'),
    path('pomodoro/', views.pomodoro, name='pomodoro'),
    path('management/', views.admin_dashboard, name='admin'),
    path('flashcards/', views.flashcards, name='flashcards'),
    path('habit/', views.habit, name='habit'),
    
    # Todo API
    path('todo/api/task', views_todo.get_all_task, name='api_todo_get_all_task'),
    path('todo/api/createGroup', views_todo.create_group, name='api_todo_group_create'),
    path('todo/api/updateGroup', views_todo.update_group, name='api_todo_group_update'),
    path('todo/api/deleteGroup', views_todo.delete_group, name='api_todo_group_delete'),
    path('todo/api/createTask', views_todo.create_task, name='api_todo_task_create'),
    path('todo/api/updateTask', views_todo.update_task, name='api_todo_task_update'),
    path('todo/api/toggleStatus', views_todo.toggle_status, name='api_todo_task_toggle'),
    path('todo/api/deleteTask', views_todo.delete_task, name='api_todo_task_delete'),
    path('todo/api/task/detail', views_todo.get_task_detail, name='api_todo_task_detail'),
    
    # Pomodoro API
    path('pomodoro/api/get', views_pomodoro.get_pomodoro, name='api_pomodoro_get'),
    path('pomodoro/api/start', views_pomodoro.start_session, name='api_pomodoro_start'),
    path('pomodoro/api/pause', views_pomodoro.pause_session, name='api_pomodoro_pause'),
    path('pomodoro/api/resume', views_pomodoro.resume_session, name='api_pomodoro_resume'),
    path('pomodoro/api/end', views_pomodoro.end_session, name='api_pomodoro_end'),
    path('pomodoro/api/switch', views_pomodoro.switch_session, name='api_pomodoro_switch'),
    path('pomodoro/api/update-settings', views_pomodoro.update_settings, name='api_pomodoro_settings'),
    path('pomodoro/api/history', views_pomodoro.get_history, name='api_pomodoro_history'),
    path('pomodoro/api/stats', views_pomodoro.get_stats, name='api_pomodoro_stats'),

    # Flashcard API
    path('flashcards/api', views_flashcards.handle_flashcards, name='api_flashcards_handle'),

    # Habit API
    path('habit/api/get', views_habit.get_habits, name='api_habit_get'),
    path('habit/api/create', views_habit.create_habit, name='api_habit_create'),
    path('habit/api/toggle', views_habit.toggle_habit, name='api_habit_toggle'),
    path('habit/api/update', views_habit.update_habit, name='api_habit_update'),
    path('habit/api/delete', views_habit.delete_habit, name='api_habit_delete'),
    path('habit/api/stats', views_habit.get_habit_stats, name='api_habit_stats'),

    # Calendar API
    path('calendar/api', views_calendar.handle_calendar, name='api_calendar_handle'),

    # Admin API
    path('management/api', views_admin.handle_admin, name='api_admin_handle'),
]
