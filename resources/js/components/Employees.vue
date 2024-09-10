<template>
    <div class="container">
      <div v-if="!isAdmin" class="my-4 p-3 bg-light border rounded">
        <h2 class="text-center mb-0">{{ companyName || 'No Company Assigned' }}</h2>
      </div>
  
      <h2>Employees</h2>
      <router-link :to="{ name: 'EmployeeCreate' }" class="btn btn-primary mb-3">Add Employee</router-link>
  
      <!-- Search Form -->
      <form @submit.prevent="searchEmployees" class="mt-3">
        <div class="input-group mb-3">
          <input v-model="searchQuery" type="text" class="form-control" placeholder="Search employees...">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
  
      <!-- Results Per Page Form -->
      <form @submit.prevent="fetchEmployees" class="form-inline mb-3">
        <div class="form-group mr-3">
          <label for="per_page" class="mr-2">Results per page:</label>
          <select v-model="perPage" class="form-control" @change="fetchEmployees">
            <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
        </div>
  
        <button type="button" @click="toggleTrashed" :class="trashedButtonClass" class="ml-3">
          {{ trashedButtonText }}
        </button>
      </form>
  
      <!-- Employees Table -->
      <table class="table table-bordered mt-3" v-if="employees.length">
        <thead>
          <tr>
            <th><a href="#" @click.prevent="sortEmployees('first_name')">First Name</a></th>
            <th><a href="#" @click.prevent="sortEmployees('last_name')">Last Name</a></th>
            <th><a href="#" @click.prevent="sortEmployees('email')">Email</a></th>
            <th><a href="#" @click.prevent="sortEmployees('phone')">Phone</a></th>
            <th><a href="#" @click.prevent="sortEmployees('company')">Company</a></th>
            <th style="width: 12%;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in employees" :key="employee.id" :class="{ 'table-danger': employee.trashed }">
            <td>{{ employee.first_name }}</td>
            <td>{{ employee.last_name }}</td>
            <td>{{ employee.email }}</td>
            <td>{{ employee.phone }}</td>
            <td>{{ employee.company.name || 'N/A' }}</td>
            <td>
              <div class="d-flex flex-column align-items-start">
                <div v-if="employee.trashed">
                  <button @click="restoreEmployee(employee.id)" class="btn btn-success btn-sm mb-2">Restore</button>
                  <button @click="forceDeleteEmployee(employee.id)" class="btn btn-danger btn-sm">Force Delete</button>
                </div>
                <div v-else>
                  <router-link :to="{ name: 'EmployeeEdit', params: { id: employee.id }}" class="btn btn-info btn-sm mb-2">Edit</router-link>
                  <button @click="deleteEmployee(employee.id)" class="btn btn-danger btn-sm">Delete</button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Pagination -->
      <nav aria-label="Page navigation" v-if="employees.length">
        <ul class="pagination pagination-sm justify-content-center">
          <li :class="{ 'disabled': !pagination.prev_page_url }" class="page-item">
            <a class="page-link" href="#" @click.prevent="changePage(pagination.prev_page_url)">
              <i class="fas fa-chevron-left"></i>
            </a>
          </li>
          <li v-for="page in pagination.last_page" :key="page" :class="{ 'active': pagination.current_page === page }" class="page-item">
            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
          </li>
          <li :class="{ 'disabled': !pagination.next_page_url }" class="page-item">
            <a class="page-link" href="#" @click.prevent="changePage(pagination.next_page_url)">
              <i class="fas fa-chevron-right"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script>
import axios from "axios";

export default {
  data() {
    return {
      employees: [],
      companyName: "",
      isAdmin: false,
      searchQuery: "",
      perPage: 10,
      includeTrashed: "off",
      pagination: {},
      perPageOptions: [10, 25, 50],
      sortField: "first_name",
      sortDirection: "asc",
    };
  },
  created() {
    this.fetchEmployees();
  },
  computed: {
    trashedButtonText() {
      if (this.includeTrashed === "off") return "Include Deleted";
      if (this.includeTrashed === "on") return "Only Deleted";
      return "Show All";
    },
    trashedButtonClass() {
      if (this.includeTrashed === "off") return "btn-dark";
      if (this.includeTrashed === "on") return "btn-info";
      return "btn-secondary";
    },
  },mounted() {
      this.fetchEmployees();
    },
  methods: {
     fetchEmployees() {
      axios
        .get("http://127.0.0.1:8000/api/employees", {
          params: {
            query: this.query,
            per_page: this.perPage,
      include_trashed: this.includeTrashed,
      sort: this.sort,
      direction: this.direction,
            page: this.currentPage,
          },
        })
        .then((response) => {
          this.employees = response.data.data;
      this.currentPage = response.data.current_page;
      this.totalPages = response.data.last_page;
      this.isOnFirstPage = response.data.current_page === 1;
      this.hasMorePages = response.data.next_page_url !== null;
     // this.jsonstr = JSON.stringify(response.data.data, null, 2); // Update the JSON data
          
        })
        .catch((error) => {
          console.error("Error fetching companies:", error);
        });
    },
    searchEmployees() {
      
      this.fetchEmployees();
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchCompanies();
    },
    sortEmployees(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        this.sortField = field;
        this.sortDirection = "asc";
      }
      this.fetchEmployees();
    },
    toggleTrashed() {
      if (this.includeTrashed === "off") {
        this.includeTrashed = "on";
      } else if (this.includeTrashed === "on") {
        this.includeTrashed = "only_trashed";
      } else {
        this.includeTrashed = "off";
      }
      this.fetchEmployees();
    },
    deleteEmployee(id) {
      if (confirm("Are you sure you want to delete this employee?")) {
        axios
          .delete(`/api/employees/${id}`)
          .then(() => {
            this.fetchEmployees();
          })
          .catch((error) => {
            console.error("Error deleting employee:", error);
          });
      }
    },
    restoreEmployee(id) {
      axios
        .put(`/api/employees/${id}/restore`)
        .then(() => {
          this.fetchEmployees();
        })
        .catch((error) => {
          console.error("Error restoring employee:", error);
        });
    },
    forceDeleteEmployee(id) {
      if (confirm("Are you sure you want to permanently delete this employee?")) {
        axios
          .delete(`/api/employees/${id}/force`)
          .then(() => {
            this.fetchEmployees();
          })
          .catch((error) => {
            console.error("Error force deleting employee:", error);
          });
      }
    },
    
  },
  mounted() {
    
    this.fetchEmployees();
  },
};
</script>

  