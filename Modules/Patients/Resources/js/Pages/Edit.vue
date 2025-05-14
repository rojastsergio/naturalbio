<script setup>
import { reactive, ref, computed, onMounted, watch, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StepIndicator from '@/Components/MultiStepForm/StepIndicator.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    patient: Object,
    municipalities: Array,
});

const steps = [
    { id: 1, name: 'Dirección' },
    { id: 2, name: 'Datos Personales' },
    { id: 3, name: 'Foto' },
    { id: 4, name: 'Firma' },
    { id: 5, name: 'Signos Vitales' },
    { id: 6, name: 'Opcionales' },
    { id: 7, name: 'Finalizar' },
];

const currentStep = ref(1);
// Unidad de peso predeterminada en 'lb'
const weightUnit = ref('lb'); 
const photoInput = ref(null);
const cameraInput = ref(null);
// Almacena la foto actual para mostrarla
const photoPreview = ref(props.patient.photo_path ? `/storage/${props.patient.photo_path}` : null);
const signaturePad = ref(null);
const isDrawing = ref(false);
const startPoint = reactive({ x: 0, y: 0 });
const departmentName = ref('');
const municipalitySearchQuery = ref('');
const showMunicipalityDropdown = ref(false);
const selectedMunicipalityObj = ref(null);

// Filtrar municipios según el texto de búsqueda
const filteredMunicipalities = computed(() => {
    if (!municipalitySearchQuery.value || municipalitySearchQuery.value.length < 2) {
        return props.municipalities.slice(0, 10); // Mostrar los primeros 10 por defecto
    }
    
    const searchTerm = municipalitySearchQuery.value.toLowerCase();
    return props.municipalities
        .filter(m => 
            m.name.toLowerCase().includes(searchTerm) || 
            m.department.name.toLowerCase().includes(searchTerm)
        )
        .slice(0, 20); // Limitar a 20 resultados para mejor rendimiento
});

// Función para manejar el evento blur del campo de búsqueda de municipios
function handleMunicipalityBlur() {
    // Esperar un momento antes de ocultar el dropdown para permitir que el clic en un ítem se procese
    setTimeout(() => {
        showMunicipalityDropdown.value = false;
    }, 200);
}

// Seleccionar un municipio de la lista desplegable
function selectMunicipality(municipality) {
    form.municipality_id = municipality.id;
    selectedMunicipalityObj.value = municipality;
    municipalitySearchQuery.value = municipality.name;
    
    // Actualizar el departamento
    if (municipality.department) {
        departmentName.value = municipality.department.name;
    } else {
        departmentName.value = '';
    }
    
    showMunicipalityDropdown.value = false;
}

// Función para actualizar el departamento cuando se selecciona un municipio
function updateDepartment() {
    if (!form.municipality_id) {
        departmentName.value = '';
        return;
    }
    
    // Buscar el municipio seleccionado en el array de municipios
    const selectedMunicipality = props.municipalities.find(m => Number(m.id) === Number(form.municipality_id));
    
    // Si encontramos el municipio y tiene departamento asociado
    if (selectedMunicipality && selectedMunicipality.department) {
        departmentName.value = selectedMunicipality.department.name;
        municipalitySearchQuery.value = selectedMunicipality.name;
        selectedMunicipalityObj.value = selectedMunicipality;
    } else {
        departmentName.value = '';
    }
}

// Si el paciente ya tiene peso, convertirlo a 'lb' (si está en 'kg')
onMounted(() => {
    // Inicializar el campo de municipio y departamento con los datos existentes
    if (form.municipality_id) {
        updateDepartment();
    }
    
    if (form.vital_signs && form.vital_signs.weight) {
        // Convertir de kg a lb ya que la unidad predeterminada es 'lb'
        form.vital_signs.weight = (parseFloat(form.vital_signs.weight) * 2.20462).toFixed(2);
    }
    
    // También inicializamos el canvas de firma si estamos en ese paso
    if (currentStep.value === 4 && signaturePad.value) {
        initSignaturePad();
    }
});

// Conversión entre unidades
const convertedWeight = computed(() => {
    if (!form.vital_signs.weight) return '';
    
    return form.vital_signs.weight;
});

function toggleWeightUnit() {
    weightUnit.value = weightUnit.value === 'kg' ? 'lb' : 'kg';
    
    // Convertir el valor del peso al cambiar la unidad
    if (form.vital_signs.weight) {
        if (weightUnit.value === 'lb') {
            form.vital_signs.weight = (parseFloat(form.vital_signs.weight) * 2.20462).toFixed(2);
        } else {
            form.vital_signs.weight = (parseFloat(form.vital_signs.weight) / 2.20462).toFixed(2);
        }
    }
}

function updateWeight(value) {
    form.vital_signs.weight = value;
}

function stopCamera(stream) {
    // Detener todas las pistas del stream
    if (stream) {
        stream.getTracks().forEach(track => {
            track.stop();
        });
    }
}

function takePicture() {
    // Verificar si el navegador soporta mediaDevices
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Solicitar acceso a la cámara
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                // Crear un elemento de video temporal para mostrar la vista previa
                const videoElement = document.createElement('video');
                const modal = document.createElement('div');
                const captureButton = document.createElement('button');
                const cancelButton = document.createElement('button');
                
                // Configurar el modal
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
                modal.style.zIndex = '9999';
                modal.style.display = 'flex';
                modal.style.flexDirection = 'column';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                
                // Configurar el video
                videoElement.srcObject = stream;
                videoElement.style.width = '100%';
                videoElement.style.maxWidth = '500px';
                videoElement.style.borderRadius = '8px';
                videoElement.style.marginBottom = '20px';
                videoElement.autoplay = true;
                
                // Configurar botones
                captureButton.textContent = 'Capturar';
                captureButton.style.padding = '10px 20px';
                captureButton.style.backgroundColor = '#4CAF50';
                captureButton.style.color = 'white';
                captureButton.style.border = 'none';
                captureButton.style.borderRadius = '4px';
                captureButton.style.marginRight = '10px';
                captureButton.style.cursor = 'pointer';
                
                cancelButton.textContent = 'Cancelar';
                cancelButton.style.padding = '10px 20px';
                cancelButton.style.backgroundColor = '#f44336';
                cancelButton.style.color = 'white';
                cancelButton.style.border = 'none';
                cancelButton.style.borderRadius = '4px';
                cancelButton.style.cursor = 'pointer';
                
                // Añadir elementos al modal
                const buttonContainer = document.createElement('div');
                buttonContainer.appendChild(captureButton);
                buttonContainer.appendChild(cancelButton);
                
                modal.appendChild(videoElement);
                modal.appendChild(buttonContainer);
                document.body.appendChild(modal);
                
                // Función para capturar la foto
                captureButton.addEventListener('click', function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = videoElement.videoWidth;
                    canvas.height = videoElement.videoHeight;
                    canvas.getContext('2d').drawImage(videoElement, 0, 0);
                    
                    // Convertir la imagen a base64
                    const dataURL = canvas.toDataURL('image/png');
                    
                    // Crear un archivo a partir de la imagen
                    fetch(dataURL)
                        .then(res => res.blob())
                        .then(blob => {
                            const file = new File([blob], "camera-photo.png", { type: "image/png" });
                            
                            // Actualizar el valor del campo de archivo
                            form.photo = file;
                            
                            // Establecer la vista previa
                            photoPreview.value = dataURL;
                            
                            // Limpiar
                            stopCamera(stream);
                            document.body.removeChild(modal);
                        });
                });
                
                // Función para cancelar
                cancelButton.addEventListener('click', function() {
                    stopCamera(stream);
                    document.body.removeChild(modal);
                });
                
            })
            .catch(function(error) {
                console.error("Error al acceder a la cámara:", error);
                // Si hay error, recurrir al método tradicional
                if (cameraInput.value) {
                    cameraInput.value.click();
                }
            });
    } else {
        // Si no hay soporte para mediaDevices, recurrir al método tradicional
        if (cameraInput.value) {
            cameraInput.value.click();
        }
    }
}

function selectFromGallery() {
    if (photoInput.value) {
        photoInput.value.click();
    }
}

// Inicializar el formulario con los datos del paciente
const form = useForm({
    // Paso 1: Dirección
    municipality_id: props.patient.municipality_id || '',
    address: props.patient.address || '',
    
    // Paso 2: Datos Personales
    name: props.patient.name || '',
    last_name: props.patient.last_name || '',
    email: props.patient.email || '',
    phone: props.patient.phone || '',
    whatsapp: props.patient.whatsapp || '',
    birthdate: props.patient.birthdate || '',
    gender: props.patient.gender || '',
    status: props.patient.status || 'active',
    
    // Paso 3: Foto
    photo: null,
    
    // Paso 4: Firma
    signature: null,
    
    // Paso 5: Signos Vitales
    vital_signs: {
        blood_pressure: '',
        oxygen_saturation: '',
        blood_glucose: '',
        heart_rate: '',
        respiratory_rate: '',
        height: '',
        weight: '',
        notes: '',
    },
    
    // Paso 6: Opcionales
    medical_history: props.patient.medical_history || '',
    
    // Método para indicar que estamos actualizando, no creando un nuevo paciente
    _method: 'PUT',
});

// Asignar los signos vitales si existen
if (props.patient.latest_vital_signs) {
    const vitals = props.patient.latest_vital_signs;
    form.vital_signs = {
        blood_pressure: vitals.blood_pressure || '',
        oxygen_saturation: vitals.oxygen_saturation || '',
        blood_glucose: vitals.blood_glucose || '',
        heart_rate: vitals.heart_rate || '',
        respiratory_rate: vitals.respiratory_rate || '',
        height: vitals.height || '',
        weight: vitals.weight || '',
        notes: vitals.notes || '',
    };
}

function nextStep() {
    if (currentStep.value < steps.length) {
        currentStep.value++;
        
        // Si nos movemos al paso de la firma, inicializamos el pad
        if (currentStep.value === 4) {
            nextTick(() => {
                initSignaturePad();
            });
        }
    }
}

function previousStep() {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
}

function selectPhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    form.photo = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Observar cambios en currentStep para inicializar el pad cuando sea necesario
watch(currentStep, (newStep) => {
    if (newStep === 4) {
        // Usamos nextTick para asegurarnos de que el DOM se ha actualizado
        nextTick(() => {
            initSignaturePad();
        });
    }
});

function initSignaturePad() {
    const canvas = signaturePad.value;
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Limpiamos los event listeners existentes para evitar duplicados
    canvas.removeEventListener('mousedown', startDrawing);
    canvas.removeEventListener('mousemove', draw);
    canvas.removeEventListener('mouseup', stopDrawing);
    canvas.removeEventListener('mouseout', stopDrawing);
    canvas.removeEventListener('touchstart', startDrawingTouch);
    canvas.removeEventListener('touchmove', drawTouch);
    canvas.removeEventListener('touchend', stopDrawing);
    
    // Clear canvas
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Si hay una firma existente, cargarla en el canvas
    if (props.patient.signature_path) {
        const img = new Image();
        img.onload = () => {
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            // Guardar la firma existente en el formulario
            saveSignature();
        };
        img.src = `/storage/${props.patient.signature_path}`;
        img.onerror = (error) => {
            console.error("Error cargando la imagen de firma:", error);
        };
    }
    
    // Set up event listeners for mouse
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events
    canvas.addEventListener('touchstart', startDrawingTouch, { passive: false });
    canvas.addEventListener('touchmove', drawTouch, { passive: false });
    canvas.addEventListener('touchend', stopDrawing);
}

function startDrawing(e) {
    isDrawing.value = true;
    startPoint.x = e.offsetX;
    startPoint.y = e.offsetY;
}

function startDrawingTouch(e) {
    e.preventDefault();
    const rect = e.target.getBoundingClientRect();
    const touch = e.touches[0];
    startPoint.x = touch.clientX - rect.left;
    startPoint.y = touch.clientY - rect.top;
    isDrawing.value = true;
}

function draw(e) {
    if (!isDrawing.value) return;
    
    const ctx = signaturePad.value.getContext('2d');
    ctx.beginPath();
    ctx.moveTo(startPoint.x, startPoint.y);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.stroke();
    
    startPoint.x = e.offsetX;
    startPoint.y = e.offsetY;
}

function drawTouch(e) {
    e.preventDefault();
    if (!isDrawing.value) return;
    
    const rect = e.target.getBoundingClientRect();
    const touch = e.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    
    const ctx = signaturePad.value.getContext('2d');
    ctx.beginPath();
    ctx.moveTo(startPoint.x, startPoint.y);
    ctx.lineTo(x, y);
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.stroke();
    
    startPoint.x = x;
    startPoint.y = y;
}

function stopDrawing() {
    if (isDrawing.value) {
        isDrawing.value = false;
        saveSignature();
    }
}

function saveSignature() {
    const dataURL = signaturePad.value.toDataURL('image/png');
    form.signature = dataURL;
}

function clearSignature() {
    const canvas = signaturePad.value;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    form.signature = null;
}

function submitForm() {
    // Si estamos en libras, convertir a kg antes de enviar al servidor
    if (weightUnit.value === 'lb' && form.vital_signs.weight) {
        const kgValue = (parseFloat(form.vital_signs.weight) / 2.20462).toFixed(2);
        form.vital_signs.weight = kgValue;
    }
    
    form.post(route('patients.update', props.patient.id), {
        onSuccess: () => {
            // No reset en caso de edición, mantener los datos
        },
    });
}
</script>

<template>
    <AppLayout title="Editar Paciente">
        <Head title="Editar Paciente" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Paciente: {{ patient.name }} {{ patient.last_name }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <StepIndicator
                        :steps="steps"
                        :current-step="currentStep"
                        class="mb-8"
                    />

                    <!-- Paso 1: Dirección -->
                    <div v-if="currentStep === 1" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="municipality_search" value="Municipio" />
                                <div class="relative">
                                    <input
                                        id="municipality_search"
                                        type="text"
                                        v-model="municipalitySearchQuery"
                                        class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:ring-naturalbio-verde focus:border-naturalbio-verde"
                                        placeholder="Buscar municipio..."
                                        autocomplete="off"
                                        @focus="showMunicipalityDropdown = true"
                                        @blur="handleMunicipalityBlur"
                                    />
                                    
                                    <!-- Icono de búsqueda -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Lista de resultados -->
                                    <div v-if="showMunicipalityDropdown && filteredMunicipalities.length > 0" 
                                         class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-auto">
                                        <ul class="py-1">
                                            <li 
                                                v-for="municipality in filteredMunicipalities" 
                                                :key="municipality.id"
                                                @mousedown="selectMunicipality(municipality)"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100"
                                            >
                                                <div class="font-medium">{{ municipality.name }}</div>
                                                <div class="text-sm text-gray-500">{{ municipality.department.name }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <InputError :message="form.errors.municipality_id" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="department" value="Departamento" />
                                <TextInput
                                    id="department"
                                    v-model="departmentName"
                                    type="text"
                                    class="w-full mt-1 bg-gray-100"
                                    readonly
                                    disabled
                                />
                            </div>
                        </div>
                        
                        <div>
                            <InputLabel for="address" value="Dirección" />
                            <TextareaInput
                                id="address"
                                v-model="form.address"
                                class="w-full mt-1"
                                :rows="3"
                            />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 2: Datos Personales -->
                    <div v-if="currentStep === 2" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="name" value="Nombre" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full mt-1"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="last_name" value="Apellidos" />
                                <TextInput
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="w-full mt-1"
                                    required
                                />
                                <InputError :message="form.errors.last_name" class="mt-2" />
                            </div>
                        </div>
                        
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full mt-1"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="whatsapp" value="WhatsApp" />
                                <TextInput
                                    id="whatsapp"
                                    v-model="form.whatsapp"
                                    type="text"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors.whatsapp" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="birthdate" value="Fecha de Nacimiento" />
                                <TextInput
                                    id="birthdate"
                                    v-model="form.birthdate"
                                    type="date"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors.birthdate" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="gender" value="Género" />
                                <SelectInput
                                    id="gender"
                                    v-model="form.gender"
                                    :options="[
                                        { value: 'male', label: 'Masculino' },
                                        { value: 'female', label: 'Femenino' },
                                        { value: 'other', label: 'Otro' },
                                    ]"
                                    placeholder="Seleccione un género"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors.gender" class="mt-2" />
                            </div>
                        </div>
                        
                        <div>
                            <InputLabel for="status" value="Estado" />
                            <SelectInput
                                id="status"
                                v-model="form.status"
                                :options="[
                                    { value: 'active', label: 'Activo' },
                                    { value: 'inactive', label: 'Inactivo' },
                                ]"
                                class="w-full mt-1"
                            />
                            <InputError :message="form.errors.status" class="mt-2" />
                        </div>

                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 3: Foto -->
                    <div v-if="currentStep === 3" class="space-y-6">
                        <div class="flex flex-col items-center">
                            <!-- Mostrar la foto actual si existe -->
                            <div v-if="photoPreview" class="mb-6">
                                <h3 class="text-center font-semibold text-gray-700 mb-2">
                                    {{ form.photo ? 'Nueva foto seleccionada' : 'Foto actual del paciente' }}
                                </h3>
                                <div class="w-64 h-64 mx-auto bg-gray-200 rounded-full overflow-hidden">
                                    <img :src="photoPreview" alt="Foto del paciente" class="w-full h-full object-cover">
                                </div>
                            </div>
                            
                            <!-- Si no hay foto, mostrar un mensaje -->
                            <div v-else class="text-center text-gray-500 mb-6">
                                <p>El paciente no tiene una foto actualmente</p>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                                <button 
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-naturalbio-verde text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde transition"
                                    @click="takePicture"
                                >
                                    <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    Tomar Nueva Foto
                                </button>
                                
                                <button 
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition"
                                    @click="selectFromGallery"
                                >
                                    <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    Seleccionar Archivo
                                </button>
                            </div>
                            
                            <!-- Inputs ocultos para la cámara y la selección de archivos -->
                            <input
                                id="photo_camera"
                                ref="cameraInput"
                                type="file"
                                accept="image/*"
                                capture="environment"
                                class="hidden"
                                @change="selectPhoto"
                            />
                            
                            <input
                                id="photo_gallery"
                                ref="photoInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="selectPhoto"
                            />
                            
                            <InputError :message="form.errors.photo" class="mt-2" />
                        </div>

                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 4: Firma -->
                    <div v-if="currentStep === 4" class="space-y-6">
                        <div class="flex flex-col items-center">
                            <!-- Título para la firma -->
                            <h3 class="text-center font-semibold text-gray-700 mb-2">
                                {{ patient.signature_path ? 'Firma actual del paciente' : 'Agregar firma' }}
                            </h3>
                            
                            <!-- Canvas para la firma -->
                            <div class="border border-gray-300 rounded-md">
                                <canvas
                                    ref="signaturePad"
                                    width="500"
                                    height="200"
                                    class="cursor-crosshair"
                                ></canvas>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Use el mouse o su dedo (en dispositivos táctiles) para firmar en el recuadro arriba.
                                </p>
                                <button
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition ease-in-out duration-150"
                                    @click="clearSignature"
                                >
                                    Borrar Firma
                                </button>
                            </div>
                            <InputError :message="form.errors.signature" class="mt-2" />
                        </div>

                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 5: Signos Vitales -->
                    <div v-if="currentStep === 5" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="blood_pressure" value="Presión Arterial (PA)" />
                                <TextInput
                                    id="blood_pressure"
                                    v-model="form.vital_signs.blood_pressure"
                                    type="text"
                                    placeholder="Ej: 120/80"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.blood_pressure']" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="oxygen_saturation" value="Saturación de Oxígeno (SpO₂)" />
                                <TextInput
                                    id="oxygen_saturation"
                                    v-model="form.vital_signs.oxygen_saturation"
                                    type="number"
                                    placeholder="Ej: 98"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.oxygen_saturation']" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="blood_glucose" value="Glucosa en Sangre" />
                                <TextInput
                                    id="blood_glucose"
                                    v-model="form.vital_signs.blood_glucose"
                                    type="number"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.blood_glucose']" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="heart_rate" value="Frecuencia Cardíaca (FC)" />
                                <TextInput
                                    id="heart_rate"
                                    v-model="form.vital_signs.heart_rate"
                                    type="number"
                                    placeholder="lpm"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.heart_rate']" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="respiratory_rate" value="Frecuencia Respiratoria (FR)" />
                                <TextInput
                                    id="respiratory_rate"
                                    v-model="form.vital_signs.respiratory_rate"
                                    type="number"
                                    placeholder="rpm"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.respiratory_rate']" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="height" value="Estatura (cm)" />
                                <TextInput
                                    id="height"
                                    v-model="form.vital_signs.height"
                                    type="number"
                                    class="w-full mt-1"
                                />
                                <InputError :message="form.errors['vital_signs.height']" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="weight" value="Peso" />
                                <div class="flex items-center">
                                    <TextInput
                                        id="weight"
                                        :modelValue="convertedWeight"
                                        @update:modelValue="updateWeight"
                                        type="number"
                                        step="0.01"
                                        class="w-full mt-1"
                                    />
                                    <button 
                                        type="button" 
                                        class="ml-2 px-3 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 focus:outline-none"
                                        @click="toggleWeightUnit"
                                    >
                                        {{ weightUnit.toLowerCase() }}
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Clic en el botón para cambiar entre {{ weightUnit.toLowerCase() === 'kg' ? 'kilogramos y libras' : 'libras y kilogramos' }}
                                </p>
                                <InputError :message="form.errors['vital_signs.weight']" class="mt-2" />
                            </div>
                        </div>
                        
                        <div>
                            <InputLabel for="vital_notes" value="Notas" />
                            <TextareaInput
                                id="vital_notes"
                                v-model="form.vital_signs.notes"
                                class="w-full mt-1"
                                :rows="3"
                            />
                            <InputError :message="form.errors['vital_signs.notes']" class="mt-2" />
                        </div>

                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 6: Opcionales -->
                    <div v-if="currentStep === 6" class="space-y-6">
                        <div>
                            <InputLabel for="medical_history" value="Historial Médico" />
                            <TextareaInput
                                id="medical_history"
                                v-model="form.medical_history"
                                class="w-full mt-1"
                                :rows="6"
                                placeholder="Ingrese antecedentes médicos, alergias, condiciones crónicas, etc."
                            />
                            <InputError :message="form.errors.medical_history" class="mt-2" />
                        </div>

                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton @click="nextStep">
                                Siguiente
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Paso 7: Finalizar -->
                    <div v-if="currentStep === 7" class="space-y-6">
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg border border-green-200 dark:border-green-700">
                            <h3 class="text-xl font-semibold text-naturalbio-verde dark:text-green-400 mb-4">
                                Resumen de Actualización
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="font-semibold">Nombre completo:</p>
                                    <p>{{ form.name }} {{ form.last_name }}</p>
                                </div>
                                
                                <div>
                                    <p class="font-semibold">Dirección:</p>
                                    <p>{{ form.address || 'No proporcionada' }}</p>
                                    <p>Municipio: {{ selectedMunicipalityObj?.name || props.municipalities.find(m => m.id === form.municipality_id)?.name || 'No seleccionado' }}</p>
                                    <p>Departamento: {{ departmentName || 'No disponible' }}</p>
                                </div>
                                
                                <div>
                                    <p class="font-semibold">Contacto:</p>
                                    <p>Email: {{ form.email || 'No proporcionado' }}</p>
                                    <p>Teléfono: {{ form.phone || 'No proporcionado' }}</p>
                                    <p>WhatsApp: {{ form.whatsapp || 'No proporcionado' }}</p>
                                </div>
                                
                                <div>
                                    <p class="font-semibold">Estado:</p>
                                    <p>{{ form.status === 'active' ? 'Activo' : 'Inactivo' }}</p>
                                </div>
                                
                                <div v-if="photoPreview">
                                    <p class="font-semibold">Foto:</p>
                                    <img :src="photoPreview" alt="Foto" class="w-20 h-20 object-cover rounded-full mt-1">
                                </div>
                                
                                <div v-if="form.signature">
                                    <p class="font-semibold">Firma:</p>
                                    <img :src="form.signature" alt="Firma" class="h-20 mt-1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <SecondaryButton @click="previousStep">
                                Atrás
                            </SecondaryButton>
                            <PrimaryButton
                                @click="submitForm"
                                :disabled="form.processing"
                                :class="{ 'opacity-50': form.processing }"
                            >
                                {{ form.processing ? 'Procesando...' : 'Actualizar Paciente' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>