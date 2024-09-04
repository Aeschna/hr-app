<template>
    <div class="container">
      <h2>Create Company</h2>
  
      <form @submit.prevent="submitForm" enctype="multipart/form-data">
        <!-- Name -->
        <div class="form-group">
          <label for="name">Name: <span class="text-danger">*</span></label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.name }"
            id="name"
            v-model="form.name"
          />
          <div v-if="errors.name" class="invalid-feedback">
            {{ errors.name[0] }}
          </div>
        </div>
  
        <!-- Address -->
        <div class="form-group">
          <label for="address">Address:</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.address }"
            id="address"
            v-model="form.address"
          />
          <div v-if="errors.address" class="invalid-feedback">
            {{ errors.address[0] }}
          </div>
        </div>
  
        <!-- Phone -->
        <div class="form-group">
          <label for="phone">Phone:</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.phone }"
            id="phone"
            v-model="form.phone"
          />
          <div v-if="errors.phone" class="invalid-feedback">
            {{ errors.phone[0] }}
          </div>
        </div>
  
        <!-- Email -->
        <div class="form-group">
          <label for="email">Email:</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors.email }"
            id="email"
            v-model="form.email"
          />
          <div v-if="errors.email" class="invalid-feedback">
            {{ errors.email[0] }}
          </div>
        </div>
  
        <!-- Logo -->
        <div class="form-group">
          <label for="logo">Logo:</label>
          <input
            type="file"
            class="form-control"
            :class="{ 'is-invalid': errors.logo }"
            id="logo"
            @change="handleFileUpload"
          />
          <div v-if="errors.logo" class="invalid-feedback">
            {{ errors.logo[0] }}
          </div>
        </div>
  
        <!-- Website -->
        <div class="form-group">
          <label for="website">Website:</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors.website }"
            id="website"
            v-model="form.website"
          />
          <div v-if="errors.website" class="invalid-feedback">
            {{ errors.website[0] }}
          </div>
        </div>
  
        <!-- User Selection -->
        <div class="form-group">
          <label for="user_id">Select User: <span class="text-danger">*</span></label>
          <select
            v-model="form.user_id"
            id="user_id"
            class="form-control"
            :class="{ 'is-invalid': errors.user_id }"
          >
            <option value="">Select a user</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
          <div v-if="errors.user_id" class="invalid-feedback">
            {{ errors.user_id[0] }}
          </div>
        </div>
  
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        form: {
          name: '',
          address: '',
          phone: '',
          email: '',
          logo: null,
          website: '',
          user_id: ''
        },
        users: [],
        errors: {}
      };
    },
    methods: {
      async fetchUsers() {
        try {
          const response = await axios.get('/api/users');
          this.users = response.data;
        } catch (error) {
          console.error('Failed to load users:', error);
        }
      },
      handleFileUpload(event) {
        this.form.logo = event.target.files[0];
      },
      async submitForm() {
        const formData = new FormData();
        for (const key in this.form) {
          formData.append(key, this.form[key]);
        }
  
        try {
          const response = await axios.post('/api/companies', formData);
          // Handle successful response
          console.log('Company created successfully:', response.data);
          // Redirect or show success message
        } catch (error) {
          if (error.response && error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            console.error('An unexpected error occurred:', error);
          }
        }
      }
    },
    mounted() {
      this.fetchUsers();
    }
  };
  </script>
  
  <style scoped>
  /* Add any additional styles here */
  </style>
  