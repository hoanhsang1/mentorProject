# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey and OneToOneField has `on_delete` set to the desired behavior
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from django.db import models


class Calendar(models.Model):
    calendar_id = models.CharField(primary_key=True, max_length=36)
    name = models.CharField(max_length=255)
    color = models.CharField(max_length=7, blank=True, null=True)
    description = models.TextField(blank=True, null=True)
    is_deleted = models.IntegerField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    user = models.OneToOneField('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'calendar'


class Event(models.Model):
    event_id = models.CharField(primary_key=True, max_length=36)
    title = models.CharField(max_length=255)
    description = models.TextField(blank=True, null=True)
    start_at = models.DateTimeField()
    end_at = models.DateTimeField(blank=True, null=True)
    is_all_day = models.IntegerField()
    location = models.CharField(max_length=255, blank=True, null=True)
    event_type = models.CharField(max_length=8, blank=True, null=True)
    repeat_pattern = models.CharField(max_length=50, blank=True, null=True)
    repeat_until = models.DateTimeField(blank=True, null=True)
    status = models.CharField(max_length=9, blank=True, null=True)
    priority = models.CharField(max_length=6, blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    calendar = models.ForeignKey(Calendar, models.DO_NOTHING)
    task = models.ForeignKey('Task', models.DO_NOTHING, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'event'


class EventReminder(models.Model):
    reminder_id = models.CharField(primary_key=True, max_length=36)
    event = models.ForeignKey(Event, models.DO_NOTHING)
    remind_at = models.DateTimeField()
    remind_type = models.CharField(max_length=12, blank=True, null=True)
    is_sent = models.IntegerField()
    created_at = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'event_reminder'


class Flashcard(models.Model):
    flashcard_id = models.CharField(primary_key=True, max_length=36)
    created_at = models.DateTimeField()
    user = models.ForeignKey('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'flashcard'


class Flashcarditem(models.Model):
    card_id = models.CharField(primary_key=True, max_length=36)
    question = models.CharField(max_length=150)
    answer = models.CharField(max_length=150)
    learned = models.IntegerField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    set = models.ForeignKey('Flashcardset', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'flashcarditem'


class Flashcardprogress(models.Model):
    progress_id = models.CharField(primary_key=True, max_length=36)
    status = models.CharField(max_length=32)
    last_reviewed = models.DateTimeField(blank=True, null=True)
    created_at = models.DateTimeField()
    card = models.ForeignKey(Flashcarditem, models.DO_NOTHING)
    user = models.ForeignKey('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'flashcardprogress'
        unique_together = (('card', 'user'),)


class Flashcardset(models.Model):
    set_id = models.CharField(primary_key=True, max_length=36)
    title = models.CharField(max_length=255)
    created_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    flashcard = models.ForeignKey(Flashcard, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'flashcardset'


class Habit(models.Model):
    habit_id = models.CharField(primary_key=True, max_length=36)
    created_at = models.DateTimeField()
    user = models.ForeignKey('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'habit'





class Habitlist(models.Model):
    habitlist_id = models.CharField(primary_key=True, max_length=36)
    name = models.CharField(max_length=255)
    color = models.CharField(max_length=20, blank=True, null=True)
    daily_target = models.PositiveIntegerField()
    created_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    habit = models.ForeignKey(Habit, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'habitlist'


class Habitlistlog(models.Model):
    log_id = models.CharField(primary_key=True, max_length=36)
    date = models.DateField()
    status = models.CharField(max_length=32)
    notes = models.CharField(max_length=100, blank=True, null=True)
    created_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    habitlist = models.ForeignKey(Habitlist, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'habitlistlog'
        unique_together = (('habitlist', 'date'),)


class Pomodoro(models.Model):
    pomodoro_id = models.CharField(primary_key=True, max_length=36)
    title = models.CharField(max_length=255)
    status = models.CharField(max_length=36)
    work_duration = models.IntegerField()
    break_duration = models.IntegerField()
    current_session = models.CharField(max_length=36)
    sessions_completed = models.IntegerField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    user = models.ForeignKey('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'pomodoro'


class Pomodorohistory(models.Model):
    history_id = models.CharField(primary_key=True, max_length=36)
    start_time = models.DateTimeField()
    end_time = models.DateTimeField(blank=True, null=True)
    duration_minutes = models.PositiveIntegerField()
    study_topic = models.CharField(max_length=255, blank=True, null=True)
    status = models.CharField(max_length=12)
    created_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    pomodoro = models.ForeignKey(Pomodoro, models.DO_NOTHING, blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)
    task = models.ForeignKey('Task', models.DO_NOTHING, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'pomodorohistory'


class Task(models.Model):
    task_id = models.CharField(primary_key=True, max_length=36)
    title = models.CharField(max_length=255)
    description = models.CharField(max_length=500, blank=True, null=True)
    priority = models.CharField(max_length=6, blank=True, null=True)
    deadline = models.DateField(blank=True, null=True)
    status = models.CharField(max_length=32)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    group = models.ForeignKey('Todolistgroup', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'task'


class Todolist(models.Model):
    todolist_id = models.CharField(primary_key=True, max_length=36)
    created_at = models.DateTimeField()
    user = models.ForeignKey('User', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'todolist'


class Todolistgroup(models.Model):
    group_id = models.CharField(primary_key=True, max_length=36)
    title = models.CharField(max_length=255)
    created_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    todolist = models.ForeignKey(Todolist, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'todolistgroup'


class User(models.Model):
    user_id = models.CharField(primary_key=True, max_length=36)
    username = models.CharField(unique=True, max_length=150)
    fullname = models.CharField(max_length=150)
    email = models.CharField(unique=True, max_length=255, blank=True, null=True)
    password = models.CharField(max_length=255)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    is_deleted = models.IntegerField()
    role = models.CharField(max_length=36, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'user'


class UsersAvatar(models.Model):
    user = models.OneToOneField(User, models.DO_NOTHING, primary_key=True)
    path = models.CharField(unique=True, max_length=500)

    class Meta:
        managed = False
        db_table = 'users_avatar'
