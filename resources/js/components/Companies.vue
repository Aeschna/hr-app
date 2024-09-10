<template>
    <div class="container">
      <h2>Companies</h2>
      <router-link to="/companies/create" class="btn btn-primary">Add Company</router-link>
    




    
      <!-- Search Form -->
      <form @submit.prevent="searchCompanies" class="mt-3">
        <div class="input-group mb-3">
          <input
            type="text"
            v-model="query"
            class="form-control"
            placeholder="Search companies..."
          />
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>
  
      <!-- Dropdown Button for Results Per Page and Include Deleted Button -->
      <div class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select v-model="perPage" class="form-control" @change="fetchCompanies">
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
  
        <!-- Button to Apply Include Deleted -->
        <button
          :class="{
            'btn-info': includeTrashed === 'only_trashed',
            'btn-dark': includeTrashed === 'on',
            'btn-secondary': includeTrashed === 'off',
          }"
          class="btn ml-3"
          @click="toggleIncludeTrashed"
        >
          {{ includeTrashed === 'only_trashed'
            ? 'Show All'
            : includeTrashed === 'on'
            ? 'Only Deleted'
            : 'Include Deleted' }}
        </button>
      </div>
  
      <!-- Company Table -->
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th @click="sortCompanies('name')">Name</th>
            <th @click="sortCompanies('email')">Email</th>
            <th @click="sortCompanies('logo')">Logo</th>
            <th @click="sortCompanies('website')">Website</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="company in companies"
            :key="company.id"
            :class="{ 'table-danger': company.trashed }"
          >
            <td>{{ company.name }}</td>
            <td>{{ company.email }}</td>
            <td>
              <img
                v-if="company.logo"
                :src="`/storage/${company.logo}`"
                alt="Company Logo"
                class="img-fluid rounded mx-auto d-block"
                style="max-width: 100px; max-height: 100px;"
              />
              <span v-else>N/A</span>
            </td>
            <td>{{ company.website }}</td>
            <td>
              <template v-if="company.trashed">
                <button class="btn btn-success" @click="restoreCompany(company.id)">
                  Restore
                </button>
                <button
                  class="btn btn-danger"
                  @click="forceDeleteCompany(company.id)"
                >
                  Force Delete
                </button>
              </template>
              <template v-else>
                <router-link :to="`/companies/${company.id}/edit`" class="btn btn-info">Edit</router-link>
                <button class="btn btn-danger" @click="deleteCompany(company.id)">
                  Delete
                </button>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
  
      
      <!-- Pagination -->
      <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center">
          <li class="page-item" :class="{ disabled: isOnFirstPage }">
            <button class="page-link" @click="previousPage">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>
  
          <li
            v-for="page in totalPages"
            :key="page"
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button class="page-link" @click="changePage(page)">
              {{ page }}
            </button>
          </li>
  
          <li class="page-item" :class="{ disabled: !hasMorePages }">
            <button class="page-link" @click="nextPage">
              <i class="fas fa-chevron-right"></i>
            </button>
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
      companies: [],
      query: "",
      perPage: 10,
        perPageOptions: [10, 20, 50],
        includeTrashed: "off",
        sort: "name",
        direction: "asc",
        currentPage: 1,
        totalPages: 1,
        isOnFirstPage: true,
        hasMorePages: false,
        jsonstr: '{}'
    };
  },
  created() {
    this.fetchCompanies();
  },
  computed: {
      prettyJson() {
        try {
          const data = JSON.parse(this.jsonstr);
          return JSON.stringify(data, null, 2); // Format the JSON nicely
        } catch (e) {
          return this.jsonstr; // Return raw string if there's an error
        }
      }
    },
    mounted() {
      this.fetchCompanies();
    },

  methods: {
    fetchCompanies() {
      axios
        .get("http://127.0.0.1:8000/api/companies", {
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
          this.companies = response.data.data;
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
    searchCompanies() {
      this.currentPage = 1;
      this.fetchCompanies();
    },

    sortCompanies(column) {
      this.direction =
        this.sort === column && this.direction === "asc" ? "desc" : "asc";
      this.sort = column;
      this.fetchCompanies();
    },
    toggleIncludeTrashed() {
      if (this.includeTrashed === "off") {
        this.includeTrashed = "on";
      } else if (this.includeTrashed === "on") {
        this.includeTrashed = "only_trashed";
      } else {
        this.includeTrashed = "off";
      }
      this.fetchCompanies();
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchCompanies();
    },
    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.fetchCompanies();
      }
    },
    nextPage() {
      if (this.hasMorePages) {
        this.currentPage++;
        this.fetchCompanies();
      }
    },
    deleteCompany(id) {
      if (confirm("Are you sure you want to delete this company?")) {
        axios
          .delete(`http://127.0.0.1:8000/api/companies/${id}`)
          .then(() => {
            this.fetchCompanies();
          })
          .catch((error) => {
            console.error("Error deleting company:", error);
          });
      }
    },
    restoreCompany(id) {
      axios
        .put(`/api/companies/${id}/restore`)
        .then(() => {
          this.fetchCompanies();
        })
        .catch((error) => {
          console.error("Error restoring company:", error);
        });
    },
    forceDeleteCompany(id) {
      if (confirm("Are you sure you want to permanently delete this company?")) {
        axios
          .delete(`/api/companies/${id}/force`)
          .then(() => {
            this.fetchCompanies();
          })
          .catch((error) => {
            console.error("Error force deleting company:", error);
          });
      }
    },
    editCompany(id) {
      this.$router.push({ name: 'edit-company', params: { id: id } });
    },
  },
};
</script>

<style scoped>
.table-danger {
  background-color: #f8d7da;
}
</style>