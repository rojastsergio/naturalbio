<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Crear Nuevo Doctor
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Usuario
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    v-model="searchUser" 
                    @input="debouncedSearchUsers"
                    @focus="showUserResults = true"
                    placeholder="Buscar usuario por nombre o email..." 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  />
                  <div 
                    v-if="showUserResults && userResults.length > 0" 
                    class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto"
                  >
                    <div 
                      v-for="user in userResults" 
                      :key="user.id"
                      @click="selectUser(user)"
                      class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                    >
                      <div class="font-semibold">{{ user.name }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ user.email }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Información del usuario seleccionado -->
                <div v-if="selectedUser" class="mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                  <div class="font-semibold">{{ selectedUser.name }}</div>
                  <div class="text-sm">
                    <div>Email: {{ selectedUser.email }}</div>
                  </div>
                </div>

                <div v-if="form.errors.user_id" class="text-red-500 text-sm mt-1">
                  {{ form.errors.user_id }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Especialidad
                </label>
                <input 
                  type="text" 
                  v-model="form.specialty"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.specialty" class="text-red-500 text-sm mt-1">
                  {{ form.errors.specialty }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Número de Licencia
                </label>
                <input 
                  type="text" 
                  v-model="form.license_number"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.license_number" class="text-red-500 text-sm mt-1">
                  {{ form.errors.license_number }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Tarifa de Consulta (Q)
                </label>
                <input 
                  type="number" 
                  v-model="form.consultation_fee"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.consultation_fee" class="text-red-500 text-sm mt-1">
                  {{ form.errors.consultation_fee }}
                </div>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Biografía
                </label>
                <textarea 
                  v-model="form.biography"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                ></textarea>
                <div v-if="form.errors.biography" class="text-red-500 text-sm mt-1">
                  {{ form.errors.biography }}
                </div>
              </div>
              
              <div>
                <label class="flex items-center">
                  <input 
                    type="checkbox" 
                    v-model="form.accepts_emergencies" 
                    class="rounded border-gray-300 text-naturalbio-verde shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                  />
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Acepta emergencias</span>
                </label>
                <div v-if="form.errors.accepts_emergencies" class="text-red-500 text-sm mt-1">
                  {{ form.errors.accepts_emergencies }}
                </div>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end">
              <Link
                :href="route('doctors.index')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 mr-2 bg-gray-300 text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
              >
                Cancelar
              </Link>
              <button 
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Guardando...' : 'Guardar Doctor' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';

export default {
  components: {
    AppLayout,
    Link
  },
  data() {
    return {
      searchUser: '',
      userResults: [],
      showUserResults: false,
      selectedUser: null,
      form: useForm({
        user_id: null,
        specialty: '',
        license_number: '',
        biography: '',
        consultation_fee: 0,
        accepts_emergencies: false
      })
    };
  },
  created() {
    this.debouncedSearchUsers = debounce(this.searchUsers, 300);
    
    // Escuchar clics en el documento para cerrar resultados
    document.addEventListener('click', this.handleDocumentClick);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleDocumentClick);
  },
  methods: {
    handleDocumentClick(event) {
      const isClickInside = this.$el.contains(event.target);
      if (!isClickInside) {
        this.showUserResults = false;
      }
    },
    searchUsers() {
      if (this.searchUser.length < 2) {
        this.userResults = [];
        this.showUserResults = false;
        return;
      }
      
      axios.get('/api/users/search', {
        params: { search: this.searchUser }
      })
      .then(response => {
        this.userResults = response.data;
        this.showUserResults = true;
      })
      .catch(error => {
        console.error('Error buscando usuarios:', error);
      });
    },
    selectUser(user) {
      this.selectedUser = user;
      this.searchUser = user.name;
      this.form.user_id = user.id;
      this.showUserResults = false;
    },
    submit() {
      if (!this.form.user_id) {
        this.form.setError('user_id', 'Por favor seleccione un usuario');
        return;
      }
      
      this.form.post(route('doctors.store'), {
        onSuccess: () => {
          // Redirigir a la lista de doctores
        }
      });
    }
  }
};
</script>