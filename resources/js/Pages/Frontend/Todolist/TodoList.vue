<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">待辦清單</h1>
    <div class="mb-6 flex justify-center">
      <input
        v-model="form.title"
        @keyup.enter="addTodo"
        class="border border-gray-300 p-2 rounded-l-md w-full max-w-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        placeholder="新增待辦事項"
      />
      <button
        type="button" 
        @click="addTodo" 
        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-r-md transition duration-200 ease-in-out"
      >
        增加
      </button>
    </div>
    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
      <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
        <thead>
          <tr class="text-left">
            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">完成/未完成</th>
            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">內容</th>
            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">功能</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="todo in todos" :key="todo.id">
            <td class="border-b border-gray-200 px-6 py-4">
              <input
                type="checkbox"
                :checked="todo.completed"
                @change="updateTodo(todo)"
                class="form-checkbox h-5 w-5 text-blue-600 transition duration-150 ease-in-out"
              />
            </td>
            <td class="border-b border-gray-200 px-6 py-4">
              <span v-if="editingId !== todo.id" :class="{ 'line-through text-gray-500': todo.completed }">
                {{ todo.title }}
              </span>
              <input
                v-else
                v-model="editingTitle"
                @keyup.enter="saveEdit(todo)"
                @keyup.esc="cancelEdit"
                class="border-b border-gray-300 focus:border-blue-500 focus:outline-none w-full"
              />
            </td>
            <td class="border-b border-gray-200 px-6 py-4">
              <button 
                v-if="editingId !== todo.id"
                type="button" 
                @click="startEdit(todo)"
                class="text-blue-500 hover:text-blue-700 mr-2 transition duration-200 ease-in-out"
              >
                編輯
              </button>
              <button
                v-else
                type="button" 
                @click="saveEdit(todo)"
                class="text-green-500 hover:text-green-700 mr-2 transition duration-200 ease-in-out"
              >
                保存
              </button>
              <button
                type="button"
             @click="deleteTodo(todo.id)"
                class="text-red-500 hover:text-red-700 transition duration-200 ease-in-out"
              >
                刪除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      todos: [],
      form: {
        title: '',
      },
      editingId: null,
      editingTitle: '',
    };
  },
  mounted() {
    this.fetchTodos();
  },
  methods: {
    async fetchTodos() {
      try {
        const response = await axios.get('/api/todos');
        this.todos = response.data;
      } catch (error) {
        console.error('Error fetching todos:', error);
      }
    },
    async addTodo() {
      if (this.form.title.trim()) {
        try {
          const response = await axios.post('/api/todos', this.form);
          this.todos.push(response.data);
          this.form.title = '';
        } catch (error) {
          console.error('Error adding todo:', error);
        }
      }
    },
    async updateTodo(todo) {
      try {
        await axios.put(`/api/todos/${todo.id}`, {
          ...todo,
          completed: !todo.completed,
        });
        todo.completed = !todo.completed;
      } catch (error) {
        console.error('Error updating todo:', error);
      }
    },
    async deleteTodo(id) {
      try {
        await axios.delete(`/api/todos/${id}`);
        this.todos = this.todos.filter(todo => todo.id !== id);
      } catch (error) {
        console.error('Error deleting todo:', error);
      }
    },
    startEdit(todo) {
      this.editingId = todo.id;
      this.editingTitle = todo.title;
    },
    async saveEdit(todo) {
      if (this.editingTitle.trim() && this.editingTitle !== todo.title) {
        try {
          await axios.put(`/api/todos/${todo.id}`, {
            ...todo,
            title: this.editingTitle,
          });
          todo.title = this.editingTitle;
        } catch (error) {
          console.error('Error updating todo:', error);
        }
      }
      this.cancelEdit();
    },
    cancelEdit() {
      this.editingId = null;
      this.editingTitle = '';
    },
  },
};
</script>
