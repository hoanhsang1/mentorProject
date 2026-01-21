<style>
    .badge {
  padding: 4px 12px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 999px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #4a5568;
}

.badge-soft {
  background: #f8fafc;
  color: #4a5568;
}

.badge-medium {
  background: rgba(74,108,247,0.08);
  color: #4a6cf7;
  border-color: rgba(74,108,247,0.2);
}

.badge-strong {
  background: #4a6cf7;
  color: white;
  border-color: #4a6cf7;
}

.badge-warning {
  background: rgba(245, 158, 11, 0.3);
  color: #92400e;
}

.btn-none {
        background-color: white;
    border: none;
    cursor: pointer;
}

</style>
<!-- Todo Header -->
<div class="card my-4">
    <div class="card-header mb-4">
        <h2 class="card-title">📝 Todo List</h2>
        <button class="btn btn-primary" id="addTodoBtn" onclick="openTodoModal()">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="mr-2">
                <path d="M8 3V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Add Todo
        </button>
    </div>
    <div class="card-list scroll">
        <ul id="groupList" class="card-nav">
            <?php foreach ($groups as $group): ?>
            <li
                class="card-list-item"
                data-id="<?= htmlspecialchars($group['group_id']) ?>"
                data-editable
            >
                <input
                class="edit-input"
                type="text"
                value="<?= htmlspecialchars($group['title']) ?>"
                readonly
                />
                <span class="input-sizer"></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>


<!-- Todo List -->
<div style="height: 417px; overflow: visible;" class="card mt-4">
    <div class="card-header mb-4">
        <h3 class="card-title">Tasks</h3>
        <button class="btn btn-primary" onclick="openAddModal()"> <!-- onclick="openTaskModal()" -->
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="mr-2">
                <path d="M8 3V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>Create Task</button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="task-header-table">
                <tr class="border-b border-border">
                    <th class="text-left p-4 text-text-secondary text-sm font-medium">Task</th>
                    <th class="text-left p-4 text-text-secondary text-sm font-medium">Priority</th>
                    <th class="text-left p-4 text-text-secondary text-sm font-medium">Due Date</th>
                    <th class="text-left p-4 text-text-secondary text-sm font-medium">Status</th>
                    <th class="text-left p-4 text-text-secondary text-sm font-medium">Actions</th>
                </tr>
            </thead>
        </table>
        <div class="table-scroll scroll">
            <table class="w-full">
                <tbody class="content_task">
                    
                </tbody>

            </table>
        </div>
    </div>
</div>

<div id="addTodoModal" class="modal hidden">
  <div class="modal-overlay" onclick="closeTodoModal()"></div>

  <div class="modal-card">
    <h3 class="modal-title mb-8">Add new group</h3>

    <input 
      id="todoInput"
      type="text"
      placeholder="Enter group name..."
      class="modal-input"
    />

    <div class="modal-actions">
      <button onclick="closeTodoModal()" class="btn-ghost">Cancel</button>
      <button onclick="createTodo()" class="btn-primary">Create</button>
    </div>
  </div>
</div>

<!-- Add Task Modal -->
<div id="addTaskModal" class="modal hidden">
    <div class="modal-overlay" onclick="closeTaskModal()"></div>
    <div class="modal-card" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add New Task</h3>
            <button class="modal-close" onclick="closeTaskModal()">✕</button>
        </div>
        <div class="modal-body space-y-4">
            <div>
                <label class="form-label">Task Name *</label>
                <input 
                    type="text" 
                    id="taskTitle"
                    placeholder="Enter task name..."
                    class="modal-input"
                    required
                />
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea 
                    id="taskDescription"
                    placeholder="Enter task description..."
                    class="modal-input"
                    rows="3"
                ></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Priority</label>
                    <select id="taskPriority" class="modal-input">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Due Date</label>
                    <input 
                        type="date" 
                        id="taskdeadline"
                        class="modal-input"
                    />
                </div>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select id="taskStatus" class="modal-input">
                    <option value="pending" selected>Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label class="form-label">Group</label>
                <select id="taskGroup" class="modal-input">
                    <?php foreach ($allGroups as $group): ?>
                    <option value="<?= htmlspecialchars($group['group_id']) ?>">
                        <?= htmlspecialchars($group['title']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-actions mt-6">
            <button type="button" onclick="closeTaskModal()" class="btn-ghost">Cancel</button>
            <button type="button" id="submitTaskBtn" class="btn-primary">Create Task</button>
        </div>
    </div>
</div>

<?php

// $content = ob_get_clean();

// Include layout
// require_once __DIR__ . '/includes/layout.php';