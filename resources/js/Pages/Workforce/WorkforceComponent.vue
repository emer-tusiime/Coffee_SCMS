<template>
  <div class="workforce-container">
    <div class="workforce-header">
      <h4>Workforce Management</h4>
      <button @click="showAddModal = true" class="btn-primary">Add Member</button>
    </div>

    <div class="workforce-grid">
      <div
        v-for="(location, index) in groupedWorkforce"
        :key="index"
        class="location-card"
      >
        <h5>{{ location.name }}</h5>
        <table class="workforce-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Role</th>
              <th>Shift</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in location.members" :key="member.id">
              <td>{{ member.name }}</td>
              <td>{{ member.role }}</td>
              <td>
                {{ formatTime(member.shift_start) }} - {{ formatTime(member.shift_end) }}
              </td>
              <td>
                <button @click="editMember(member)" class="btn-edit">Edit</button>
                <button @click="deleteMember(member.id)" class="btn-delete">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="availability-section">
      <h4>Shift Availability</h4>
      <div class="availability-form">
        <select v-model="selectedLocation" @change="checkAvailability">
          <option value="">Select Location</option>
          <option v-for="location in locations" :key="location.id" :value="location.id">
            {{ location.name }}
          </option>
        </select>
        <input type="date" v-model="selectedDate" @change="checkAvailability" />
        <div class="availability-results">
          <h5>Available Members:</h5>
          <ul>
            <li v-for="member in availableMembers" :key="member.id">
              {{ member.name }} - {{ member.role }}
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay">
      <div class="modal">
        <h4>{{ isEditing ? 'Edit Member' : 'Add Member' }}</h4>
        <form @submit.prevent="saveMember">
          <div class="form-group">
            <label>Name</label>
            <input v-model="memberForm.name" required />
          </div>
          <div class="form-group">
            <label>Role</label>
            <input v-model="memberForm.role" required />
          </div>
          <div class="form-group">
            <label>Location</label>
            <select v-model="memberForm.location_id" required>
              <option value="">Select Location</option>
              <option v-for="location in locations" :key="location.id" :value="location.id">
                {{ location.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Production Line (Optional)</label>
            <select v-model="memberForm.production_line_id">
              <option value="">Select Production Line</option>
              <option v-for="line in productionLines" :key="line.id" :value="line.id">
                {{ line.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Shift Start</label>
            <input
              type="time"
              v-model="memberForm.shift_start"
              required
            />
          </div>
          <div class="form-group">
            <label>Shift End</label>
            <input
              type="time"
              v-model="memberForm.shift_end"
              required
            />
          </div>
          <div class="modal-actions">
            <button type="submit" class="btn-primary">Save</button>
            <button @click="closeModal" class="btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default defineComponent({
  data() {
    return {
      workforce: [],
      locations: [],
      productionLines: [],
      availableMembers: [],
      showAddModal: false,
      showEditModal: false,
      isEditing: false,
      memberForm: {
        id: null,
        name: '',
        role: '',
        location_id: '',
        production_line_id: '',
        shift_start: '',
        shift_end: '',
      },
      selectedLocation: '',
      selectedDate: '',
    };
  },

  computed: {
    Auth() {
      return usePage().props.auth;
    },

    groupedWorkforce() {
      const grouped = {};
      this.workforce.forEach(member => {
        if (!grouped[member.location.name]) {
          grouped[member.location.name] = {
            name: member.location.name,
            members: []
          };
        }
        grouped[member.location.name].members.push(member);
      });
      return Object.values(grouped);
    },
  },

  mounted() {
    this.loadWorkforce();
    this.loadProductionLines();
    this.listenForChanges();
  },

  methods: {
    async loadWorkforce() {
      try {
        const response = await axios.get(route('factory.workforce.status'));
        this.workforce = response.data;
      } catch (error) {
        console.error('Error loading workforce:', error);
      }
    },

    async loadProductionLines() {
      try {
        const response = await axios.get(route('factory.production.lines'));
        this.productionLines = response.data;
      } catch (error) {
        console.error('Error loading production lines:', error);
      }
    },

    async checkAvailability() {
      if (!this.selectedLocation || !this.selectedDate) return;
      
      try {
        const response = await axios.get(route('factory.workforce.availability', {
          locationId: this.selectedLocation,
          date: this.selectedDate,
        }));
        this.availableMembers = response.data;
      } catch (error) {
        console.error('Error checking availability:', error);
      }
    },

    editMember(member) {
      this.isEditing = true;
      this.memberForm = { ...member };
      this.showEditModal = true;
    },

    async deleteMember(id) {
      if (!confirm('Are you sure you want to delete this member?')) return;

      try {
        await axios.delete(route('factory.workforce.destroy', id));
        this.loadWorkforce();
      } catch (error) {
        console.error('Error deleting member:', error);
      }
    },

    async saveMember() {
      try {
        const routeName = this.isEditing ? 'factory.workforce.update' : 'factory.workforce.store';
        const response = await axios[this.isEditing ? 'put' : 'post'](
          route(routeName, this.isEditing ? this.memberForm.id : null),
          this.memberForm
        );

        this.closeModal();
        this.loadWorkforce();
      } catch (error) {
        console.error('Error saving member:', error);
      }
    },

    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.isEditing = false;
      this.memberForm = {
        id: null,
        name: '',
        role: '',
        location_id: '',
        production_line_id: '',
        shift_start: '',
        shift_end: '',
      };
    },

    listenForChanges() {
      window.Echo.private(`workforce.${this.Auth.user.id}`)
        .listen('.shift-change', (e) => {
          this.loadWorkforce();
        });
    },

    formatTime(time) {
      return new Date(`1970-01-01T${time}`).toLocaleTimeString();
    },
  },
});
</script>

<style scoped>
.workforce-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.workforce-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.workforce-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.location-card {
  background: #fff;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.workforce-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.workforce-table th,
.workforce-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
}

.workforce-table th {
  background: #f8f9fa;
  font-weight: 600;
}

.availability-section {
  background: #fff;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.availability-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.availability-results {
  margin-top: 1rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 0.5rem;
  width: 100%;
  max-width: 500px;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.form-group select,
.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.25rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-primary {
  background: #2196f3;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.btn-edit {
  background: #28a745;
  color: white;
  padding: 0.25rem 0.5rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.btn-delete {
  background: #dc3545;
  color: white;
  padding: 0.25rem 0.5rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.btn-primary:hover {
  background: #1976d2;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-edit:hover {
  background: #218838;
}

.btn-delete:hover {
  background: #c82333;
}
</style>
