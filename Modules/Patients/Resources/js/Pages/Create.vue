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
// Cambiar la unidad predeterminada a 'lb'
const weightUnit = ref('lb'); 
const photoInput = ref(null);
const cameraInput = ref(null);

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

const form = useForm({
    // Paso 1: Dirección
    municipality_id: '',
    address: '',
    
    // Paso 2: Datos Personales
    name: '',
    last_name: '',
    email: '',
    phone: '',
    whatsapp: '',
    birthdate: '',
    gender: '',
    
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
    medical_history: '',
    
    // Paso 7: No hay campos adicionales
});

const photoPreview = ref(null);
const signaturePad = ref(null);
const isDrawing = ref(false);
const startPoint = reactive({ x: 0, y: 0 });

function nextStep() {
    if (currentStep.value < steps.length) {
        currentStep.value++;
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

// Inicializar el pad de firma cuando se monta el componente
onMounted(() => {
    if (currentStep.value === 4 && signaturePad.value) {
        initSignaturePad();
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
    
    form.post(route('patients.store'), {
        onSuccess: () => {
            form.reset();
            photoPreview.value = null;
            if (signaturePad.value) {
                clearSignature();
            }
        },
    });
}
</script>

<template>
    <AppLayout title="Crear Paciente">
        <Head title="Crear Paciente" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Crear Nuevo Paciente
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
                        <div>
                            <InputLabel for="municipality_id" value="Municipio" />
                            <SelectInput
                                id="municipality_id"
                                v-model="form.municipality_id"
                                :options="municipalities"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Seleccione un municipio"
                                class="w-full mt-1"
                            />
                            <InputError :message="form.errors.municipality_id" class="mt-2" />
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
                            <div v-if="photoPreview" class="mb-4">
                                <img :src="photoPreview" alt="Vista previa" class="w-64 h-64 object-cover rounded-full">
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
                                    Tomar Foto
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
                                Resumen de Registro
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="font-semibold">Nombre completo:</p>
                                    <p>{{ form.name }} {{ form.last_name }}</p>
                                </div>
                                
                                <div>
                                    <p class="font-semibold">Contacto:</p>
                                    <p>Email: {{ form.email || 'No proporcionado' }}</p>
                                    <p>Teléfono: {{ form.phone || 'No proporcionado' }}</p>
                                    <p>WhatsApp: {{ form.whatsapp || 'No proporcionado' }}</p>
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
                                {{ form.processing ? 'Procesando...' : 'Registrar Paciente' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>