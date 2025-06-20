<template>
  <div class="inventory-container">
    <div class="inventory-header">
      <h4>Inventory Management</h4>
      <div class="inventory-actions">
        <button @click="showUpdateModal = true" class="btn-primary">Update Stock</button>
      </div>
    </div>

    <div class="inventory-alerts" v-if="lowStock.length > 0">
      <div class="alert alert-warning">
        <h5>Low Stock Alerts</h5>
        <ul>
          <li v-for="item in lowStock" :key="item.id">
            {{ item.product.name }} - {{ item.quantity }} units remaining
          </li>
        </ul>
      </div>
    </div>

    <div class="inventory-grid">
      <div
        v-for="(location, index) in groupedInventory"
        :key="index"
        class="inventory-location"
      >
        <h5>{{ location.name }}</h5>
        <table class="inventory-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Quantity</th>
              <th>Last Updated</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in location.items"
              :key="item.id"
              :class="{ 'low-stock': item.quantity <= 10 }"
            >
              <td>{{ item.product.name }}</td>
              <td>{{ item.product.category.name }}</td>
              <td>{{ item.quantity }}</td>
              <td>{{ formatTime(item.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Update Stock Modal -->
    <div v-if="showUpdateModal" class="modal-overlay">
      <div class="modal">
        <h4>Update Stock</h4>
        <form @submit.prevent="updateStock">
          <div class="form-group">
            <label for="location">Location</label>
            <select v-model="updateForm.location_id" required>
              <option value="">Select Location</option>
              <option v-for="location in locations" :key="location.id" :value="location.id">
                {{ location.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="product">Product</label>
            <select v-model="updateForm.product_id" required>
              <option value="">Select Product</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input
              type="number"
              v-model="updateForm.quantity"
              required
              min="0"
            />
          </div>

          <div class="modal-actions">
            <button type="submit" class="btn-primary">Update</button>
            <button @click="showUpdateModal = false" class="btn-secondary">Cancel</button>
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
      inventory: [],
      lowStock: [],
      locations: [],
      products: [],
      showUpdateModal: false,
      updateForm: {
        location_id: '',
        product_id: '',
        quantity: '',
      },
    };
  },

  computed: {
    Auth() {
      return usePage().props.auth;
    },

    groupedInventory() {
      const grouped = {};
      this.inventory.forEach(item => {
        if (!grouped[item.location.name]) {
          grouped[item.location.name] = {
            name: item.location.name,
            items: []
          };
        }
        grouped[item.location.name].items.push(item);
      });
      return Object.values(grouped);
    },
  },

  mounted() {
    this.loadInventory();
    this.listenForUpdates();
  },

  methods: {
    async loadInventory() {
      try {
        const response = await axios.get(route('factory.inventory.status'));
        this.inventory = response.data;
        this.loadLowStock();
      } catch (error) {
        console.error('Error loading inventory:', error);
      }
    },

    async loadLowStock() {
      try {
        const response = await axios.get(route('factory.inventory.alerts'));
        this.lowStock = response.data;
      } catch (error) {
        console.error('Error loading low stock alerts:', error);
      }
    },

    async updateStock() {
      try {
        await axios.post(route('factory.inventory.update'), this.updateForm);
        this.showUpdateModal = false;
        this.updateForm = {
          location_id: '',
          product_id: '',
          quantity: '',
        };
        this.loadInventory();
      } catch (error) {
        console.error('Error updating stock:', error);
      }
    },

    listenForUpdates() {
      window.Echo.private(`inventory.${this.Auth.user.id}`)
        .listen('.stock-update', (e) => {
          this.loadInventory();
          this.loadLowStock();
        });
    },

    formatTime(time) {
      return new Date(time).toLocaleString();
    },
  },
});
</script>

<style scoped>
.inventory-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.inventory-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.inventory-actions {
  display: flex;
  gap: 1rem;
}

.inventory-alerts {
  margin-bottom: 2rem;
}

.alert {
  padding: 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

.alert-warning {
  background-color: #fff3cd;
  border: 1px solid #ffeeba;
  color: #856404;
}

.inventory-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.inventory-location {
  background: #fff;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.inventory-table th,
.inventory-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
}

.inventory-table th {
  background: #f8f9fa;
  font-weight: 600;
}

.low-stock {
  background: #fff3cd;
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

.btn-primary:hover {
  background: #1976d2;
}

.btn-secondary:hover {
  background: #5a6268;
}
</style>
