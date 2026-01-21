const inputForm = document.getElementById("todoInput")

// Inline JavaScript for this page
document.addEventListener('DOMContentLoaded', function() {
		// Add todo button click handler
		document.getElementById('addTodoBtn').addEventListener('click', function() {
			 
				// You can implement a modal or form here
		});
		
		// Todo checkbox toggle
		document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
				checkbox.addEventListener('change', function() {
						const taskText = this.closest('tr').querySelector('span');
						if (this.checked) {
								taskText.classList.add('line-through', 'text-text-secondary');
						} else {
								taskText.classList.remove('line-through', 'text-text-secondary');
						}
				});
		});
		//changeStatusClick()
});

document.querySelectorAll('[data-editable]').forEach(item => {
	const input = item.querySelector('.edit-input')
	const sizer = item.querySelector('.input-sizer')
    const currentValue = input.value

	const resize = () => {
		sizer.textContent = input.value || ' '
		input.style.width = sizer.offsetWidth + 2 + 'px'
	}
	item.addEventListener('click', () => {
		const groupId = item.dataset.id
		getAllTask(groupId)
	})
	resize()

	input.addEventListener('input', resize)

	// DOUBLE CLICK → EDIT
	item.addEventListener('dblclick', () => {
		item.classList.add('editing')
		input.removeAttribute('readonly')
		input.focus()
		input.select()
        
	})

	const save = async () => {
		const value = input.value.trim()
        const id = item.dataset.id
        // console.log(item.dataset, item.getAttribute("data-id"))
        // XOÁ HẾT → DELETE
        if (value === '') {
            await deleteGroup(id)
            item.remove()
            return
        }

        input.value = value
        input.setAttribute('readonly', true)
        item.classList.remove('editing')
        resize()

        if (value!==currentValue) {

            await updateGroup(id, value)
        }
	}

	input.addEventListener('blur', save)

	input.addEventListener('keydown', e => {
		if (e.key === 'Enter') input.blur()
		if (e.key === 'Escape') {
			input.value = input.defaultValue
			input.blur()
		}
	})
})

function openTodoModal() {
	document.getElementById("addTodoModal").classList.remove("hidden");
	document.getElementById("todoInput").focus();
}

function closeTodoModal() {
	document.getElementById("addTodoModal").classList.add("hidden");
	inputForm.value = ''
}

// function saveTodo() {
// 	const value = document.getElementById("todoInput").value.trim();
// 	if (!value) return;

// 	alert("New group: " + value); // sau này thay bằng AJAX
// 	closeTodoModal();
// }


function renderGroup(group) {
	const ul = document.getElementById("groupList");
	
	const li = document.createElement("li");
	li.className = "card-list-item";
	li.dataset.id = group.group_id;
	li.setAttribute("data-editable", "");
	
	li.innerHTML = `
	<input
	class="edit-input"
	type="text"
	value="${group.title}"
	readonly
		/>
		<span class="input-sizer"></span>
		`;
		
		ul.appendChild(li);
		
		// ====== INLINE EDIT LOGIC ======
		const input = li.querySelector(".edit-input");
		const sizer = li.querySelector(".input-sizer");
		
		const resize = () => {
			sizer.textContent = input.value || " ";
			input.style.width = sizer.offsetWidth + 2 + "px";
		};
		
		resize();
		
		input.addEventListener("input", resize);
		
		// Double click → edit
		li.addEventListener("dblclick", () => {
			li.classList.add("editing");
		input.removeAttribute("readonly");
		input.focus();
		input.select();
	});
	
	const save = async () => {
		const value = input.value.trim();
		const id = li.dataset.id;
		
		// Xoá hết chữ → delete
		if (value === "") {
			input.blur() 
			input.onblur = null
			const ok = await deleteGroup(id);
			if (ok) li.remove()
			return;
		}
		
		input.value = value;
		input.setAttribute("readonly", true);
		li.classList.remove("editing");
		resize();
		
		await updateGroup(id, value);
	};
	
	input.addEventListener("blur", save);
	
	input.addEventListener("keydown", (e) => {
		if (e.key === "Enter") input.blur();
		if (e.key === "Escape") {
			input.value = input.defaultValue;
			input.blur();
		}
	});

    li.addEventListener("click", () => {
        const groupId = li.dataset.id;
        currentGroupId = groupId;
        getAllTask(groupId);
    });
}

async function createTodo() {
		

		const title = inputForm.value.trim();
		if (!title) {
				return showToast("Tên group không được để trống")
		}
		const res = await fetch ("/todo/api/createGroup", {
				method: "POST",
				headers: {
						"Content-Type": "application/x-www-form-urlencoded"
				},
				body:   "action=createGroup&title=" + encodeURIComponent(title)
				
		})

		// const data = await res.json();
		let data;
		try {
				data = await res.json();
		} catch (e) {
				console.error("Server returned non-JSON");
				console.log(await res.text());
				return;
		}
		if (data.success) {
				renderGroup(data.group);
				closeTodoModal();
                showToast("Đã tạo Group thành công","success")
		} else {
				showToast(data.error || "Error")
		}
}

async function updateGroup(id,title) {
	const res = await fetch ("/todo/api/updateGroup", {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"

		},
		body: `action=updateGroup&groupId=`+encodeURIComponent(id)+`&title=`+encodeURIComponent(title)
	})
	let data;
	try {
		data=await res.json();
	} catch (e) {
		console.error("Server returned non-JSON");
		console.log(await res.text());
		return;
	}

	if (data.success) {
        showToast("Đã chỉnh sữa Group thành công","success")
	} else {
		showToast(data.error || "Error")
	}
}

async function deleteGroup(id) {
	const res = await fetch ("/todo/api/deleteGroup", {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"

		},
		body: `action=deleteGroup&groupId=`+encodeURIComponent(id)
	})
	let data;
	try {
		data=await res.json();
	} catch (e) {
		console.error("Server returned non-JSON");
		console.log(await res.text());
		return;
	}

	if (data.success) {
        showToast("Đã xoá Group thành công","success")
		return data.success === true

	} else {
		showToast(data.error || "Error")
	}
}
// Other functions (keep from your existing todolist.js)
let currentGroupId = null;
async function getAllTask(id) {
    try {
        const res = await fetch(`/todo/api/task?groupId=${encodeURIComponent(id)}`, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            renderAllTask(data.tasks || []);
            currentGroupId=id
        } else {
            showToast(data.error || "Error loading tasks");
        }
    } catch (e) {
        console.error("Error:", e);
        showToast("Network error");
    }
}

function renderAllTask(tasks) {
	const tbody = document.querySelector("table tbody")
	tbody.innerHTML = ""

    if (tasks.length === 0) {
        // Nếu là empty array - không có task
        tbody.innerHTML = `
            <tr class="task-row">
                <td colspan="5">
                    <div class="empty-state-content">
                        <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="empty-state-text">No tasks yet. Click "Add Task" to create one.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    } else {

        tasks.forEach(todo => {
            const tr = document.createElement("tr")
            tr.className = "border-b border-border hover:bg-bg-input"
    
            const priorityClass = {
                high: "badge badge-strong",
                medium: "badge badge-medium",
                low: "badge badge-soft"
            }[todo.priority] || "badge"
    
            const statusClass = {
                pending: "badge badge-soft",
                in_progress: "badge badge-medium",
                completed: "badge badge-strong",
                overdue: "badge badge-warning"
            }[todo.status] || "badge"
    
            tr.innerHTML = `
                <td class="p-4">
                    <div class="flex items-center">
                        <input data-id="${todo.task_id}" type="checkbox" class="task-checkbox mr-3 h-5 w-5 rounded border-border" ${todo.status === "completed" ? "checked" : ""}>
                        <span class="${todo.status === "completed" ? "line-through text-text-secondary" : ""}">
                            ${todo.title}
                        </span>
                    </div>
                </td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${priorityClass}">
                        ${todo.priority}
                    </span>
                </td>
                <td class="p-4 text-text">${todo.deadline}</td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        ${todo.status.replace("_", " ")}
                    </span>
                </td>
                <td class="p-4 relative">
					<button class="task-action-btn" data-id="${todo.task_id}">⋯</button>

					<ul class="task-menu hidden">
						<li class="task-edit" data-id="${todo.task_id}">Edit</li>
						<li class="task-delete">Delete</li>
					</ul>
				</td>
            `
			tr.dataset.id = todo.task_id
            tbody.appendChild(tr)
        })
		//changeStatusClick();
    }

}

function refreshGroupSelect() {
  const select = document.getElementById("taskGroup");
  select.innerHTML = "";
  document.querySelectorAll("#groupList li").forEach(li => {
     const opt = document.createElement("option");
     opt.value = li.dataset.id;
     opt.textContent = li.querySelector(".edit-input").value;
     select.appendChild(opt);
  });
}


function openTaskModal(isEdit = false) {
    if (!isEdit && !currentGroupId) {
        showToast("Please select a group first", "error");
        return;
    }
    refreshGroupSelect();
	// console.log("open modal", isEdit, currentGroupId);
    document.getElementById("addTaskModal").classList.remove("hidden");
}



function closeTaskModal() {
    document.getElementById("addTaskModal").classList.add("hidden");
}

async function createTask() {
    const title = document.getElementById("taskTitle").value.trim();
    const description = document.getElementById("taskDescription").value.trim();
    const priority = document.getElementById("taskPriority").value;
    const status = document.getElementById("taskStatus").value;
    const deadline = document.getElementById("taskdeadline").value;
    const groupId = document.getElementById("taskGroup").value;
    
    if (!title) {
        showToast("Task name is required","error")
        return;
    }
    const params = new URLSearchParams();
    params.append('action', 'createTask');
    params.append('title', title);
    params.append('description', description);
    params.append('priority', priority);
    params.append('status', status);
    params.append('deadline', deadline);
    params.append('group_id', groupId);
    // console.count("CREATE TASK CALLED");
    try {
        const res = await fetch("/todo/api/createTask", {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params
        });
        
        const data = await res.json();
        
        if (data.success) {
            closeTaskModal();
            getAllTask(currentGroupId); // Refresh tasks
            showToast("Task created successfully","success")
        } else {
            showToast(data.error || "Error creating task");
        }
    } catch (error) {
        console.error("Error:", error);
        showToast("Network error","error");
    }
}


document.addEventListener("change", async function (e) {
    if (!e.target.classList.contains("task-checkbox")) return;

    const id = e.target.dataset.id;
    // console.log("clicked:", id);

    const params = new URLSearchParams();
    params.append("id", id);

    const res = await fetch("/todo/api/toggleStatus", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: params
    });

    const data = await res.json();
	// const text = await res.text();
	// console.log(text);
    if (!data.success) {
        alert("Update failed");
        return;
    }

    updateTaskUI(data.task); // hàm render lại đúng UI
});

function getPriorityClass(priority) {
  return {
    high: "badge badge-strong",
    medium: "badge badge-medium",
    low: "badge badge-soft"
  }[priority] || "badge";
}

function getStatusClass(status) {
  return {
    pending: "badge badge-soft",
    in_progress: "badge badge-medium",
    completed: "badge badge-strong",
    overdue: "badge badge-warr"
  }[status] || "badge badge-warning";
}

function updateTaskUI(todo) {
  const tr = document.querySelector(`tr[data-id="${todo.task_id}"]`);
  if (!tr) return;

  const priorityClass = getPriorityClass(todo.priority);
  const statusClass = getStatusClass(todo.status);

  tr.innerHTML = `
      <td class="p-4">
          <div data-id="${todo.task_id}" class="flex items-center">
              <input data-id="${todo.task_id}"
                  type="checkbox"
                  class="task-checkbox mr-3 h-5 w-5 rounded border-border"
                  ${todo.status === "completed" ? "checked" : ""}>
              <span class="${todo.status === "completed" ? "line-through text-text-secondary" : ""}">
                  ${todo.title}
              </span>
          </div>
      </td>
      <td class="p-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${priorityClass}">
              ${todo.priority}
          </span>
      </td>
      <td class="p-4 text-text">${todo.deadline}</td>
      <td class="p-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
              ${todo.status.replace("_", " ")}
          </span>
      	</td>
      	<td class="p-4 relative">
			<button class="task-action-btn" data-id="${todo.task_id}">⋯</button>

			<ul class="task-menu hidden">
				<li class="task-edit">Edit</li>
				<li class="task-delete">Delete</li>
			</ul>
		</td>
  `;

  changeStatusClick(); // gắn lại event
}

document.addEventListener("click", function (e) {
  // đóng tất cả menu trước
  document.querySelectorAll(".task-menu").forEach(m => m.classList.add("hidden"));

  // nếu click vào nút ...
  if (e.target.classList.contains("task-action-btn")) {
    const menu = e.target.nextElementSibling;
    menu.classList.toggle("hidden");
    e.stopPropagation();
  }

  // delete
  if (e.target.classList.contains("task-delete")) {
    const id = e.target.closest("td").querySelector(".task-action-btn").dataset.id;
    console.log("Delete:", id);
  }

  // edit
  if (e.target.classList.contains("task-edit")) {
    const id = e.target.closest("td").querySelector(".task-action-btn").dataset.id;
    console.log("Edit:", id);
  }
});

document.addEventListener("click", async function (e) {

  if (!e.target.classList.contains("task-delete")) return;

  const btn = e.target.closest("td").querySelector(".task-action-btn");
  const id = btn.dataset.id;

  if (!confirm("Delete this task?")) return;

  const params = new URLSearchParams();
  params.append("id", id);

  const res = await fetch("/todo/api/deleteTask", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params
  });

  const data = await res.json();

  if (!data.success) {
    showToast("Delete failed","error")
    return;
  }

  // xoá UI luôn, không cần reload
  document.querySelector(`tr[data-id="${id}"]`)?.remove();
});

document.addEventListener("click", async (e) => {
  const editBtn = e.target.closest(".task-edit");
  if (!editBtn) return;

  const taskId = editBtn.dataset.id;
  console.log("EDIT CLICK:", taskId);

  const res = await fetch(`/todo/api/task/detail?id=${taskId}`);
  const json = await res.json();

  if (!json.success) {
    showToast(json.error, "error");
    return;
  }

  const task = json.task;

  editingTaskId = taskId;

  taskTitle.value = task.title ?? "";
  taskDescription.value = task.description ?? "";
  taskPriority.value = task.priority ?? "medium";
  taskdeadline.value = task.deadline ? task.deadline.slice(0,10) : "";
  taskStatus.value = task.status ?? "pending";
  taskGroup.value = task.group_id ?? "";

  submitTaskBtn.textContent = "Save";

  openEditModal(taskId);
});



async function updateTask(id) {
  const params = new URLSearchParams();
  params.append("id", id);
  params.append("title", taskTitle.value);
  params.append("description", taskDescription.value);
  params.append("priority", taskPriority.value);
  params.append("deadline", taskdeadline.value);
  params.append("status", taskStatus.value);
  params.append("group_id", taskGroup.value);

  const res = await fetch("/todo/api/updateTask", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params
  });

  const data = await res.json();

  if (!data.success) {
    showToast(data.error, "error");
    return;
  }

  closeTaskModal();
  getAllTask(currentGroupId);
}



let editingTaskId = null;

const modal = document.getElementById("addTaskModal");
const submitBtn = document.getElementById("submitTaskBtn");
const editModalTitle = document.getElementById("modalTitle");

// ===== OPEN MODAL FOR CREATE =====
function openAddModal() {
  if (!currentGroupId) {
    showToast("Please select a group first", "error");
    return;
  }
  refreshGroupSelect();
  editingTaskId = null;

  taskTitle.value = "";
  taskDescription.value = "";
  taskPriority.value = "medium";
  taskdeadline.value = "";
  taskStatus.value = "pending";
  taskGroup.value = currentGroupId;

  submitBtn.textContent = "Create Task";
  editModalTitle.textContent = "Add New Task";

  modal.classList.remove("hidden");
}

// ===== OPEN MODAL FOR EDIT =====
async function openEditModal(taskId) {
  const res = await fetch(`/todo/api/task/detail?id=${taskId}`);
  const json = await res.json();

  if (!json.success) {
    showToast(json.error, "error");
    return;
  }
  refreshGroupSelect()
  const task = json.task;
  editingTaskId = taskId;

  taskTitle.value = task.title ?? "";
  taskDescription.value = task.description ?? "";
  taskPriority.value = task.priority ?? "medium";
  taskdeadline.value = task.deadline ? task.deadline.slice(0,10) : "";
  taskStatus.value = task.status ?? "pending";
  taskGroup.value = task.group_id ?? currentGroupId;

  submitBtn.textContent = "Save";
  editModalTitle.textContent = "Task Details";

  modal.classList.remove("hidden");
}

// ===== CLOSE MODAL =====
function closeTaskModal() {
  modal.classList.add("hidden");
}

// ===== SUBMIT BUTTON =====
submitBtn.addEventListener("click", async () => {
  if (editingTaskId) {
    await updateTask(editingTaskId);
  } else {
    await createTask();
  }
});


// document.addEventListener("click", e => {
//   console.log("clicked element:", e.target);
// });

document.addEventListener("click", (e) => {
  // Nếu click vào menu, button, checkbox → bỏ qua
  if (
    e.target.closest(".task-action-btn") ||
    e.target.closest(".task-menu") ||
    e.target.classList.contains("task-checkbox")
  ) return;

  const tr = e.target.closest("tr[data-id]");
  if (!tr) return;

  const taskId = tr.dataset.id;
  openEditModal(taskId);
});
