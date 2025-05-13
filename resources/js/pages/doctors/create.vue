<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {type BreadcrumbItem} from '@/types';
    import {computed, ref} from 'vue';
    import {Head, usePage, Link, router} from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import {CircleX} from 'lucide-vue-next';
    import Swal from 'sweetalert2';


    const breadcrumbs: BreadcrumbItem[] = [
        {title: 'Terapeutas', href: '/doctors'},
        {title: 'Agregar Terapeuta', href: '#'},
    ];

    const form = ref<Partial<{
        name: string;
        last_name: string;
        id_card: string;
        gender: string;
        birth_date: string;
        email: string;
        age: number;
        ethnicity: string;
        phone: string;
        university_name: string;
        degree_title: string;
        graduation_year: string;
        speciality: string;
        certifications: string;
        user_type: 'admin' | 'doctor' | 'usuario';
        status: 'aspirante' | 'paciente';
        //id_address: Address | null; 
    }>>({
        name: '',
        last_name: '',
        id_card: '',
        gender: '',
        birth_date: '',
        age: undefined,
        ethnicity: '',
        phone: '',
        email: '',
        university_name: '',
        degree_title: '',
        graduation_year: '',
        speciality: '',
        certifications: '',
        user_type: 'doctor',
        status: undefined,
        //id_address: null, // Inicializado como null
    });

   
    const validateNumberInput = (event: Event) => {
        const inputElement = event.target as HTMLInputElement;
        inputElement.value = inputElement.value.replace(/[^0-9]/g, '');
    };

    
    const currentSection = ref(1);

    const nextSection = () => {
        currentSection.value++;
    };

    const prevSection = () => {
        currentSection.value--;
    };

    const canProceed = computed(() => {
        if (currentSection.value === 1) {
            return form.value.name && form.value.last_name && form.value.id_card && form.value.gender && form.value.birth_date && form.value.age && form.value.ethnicity;
        }
        if (currentSection.value === 2) {
            return form.value.university_name && form.value.degree_title && form.value.graduation_year && form.value.speciality && form.value.certifications;
        }
        return true;
    });

    const resetForm = () => {
        Object.keys(form.value).forEach(key => {
            form.value[key as keyof typeof form.value] = undefined;
        });
        form.value.user_type = 'doctor';
        form.value.status = undefined;
        //form.value.address = null; // Resetea también la dirección
    };

    const cancel = () => {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas cancelar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, continuar',
        }).then((result) => {
            if (result.isConfirmed) {
            router.visit(route('doctor_therapies.index'));
            }
        });
    };


    const submit = () => {
        if (!canProceed.value) {
            Swal.fire({
            title: 'Error',
            text: 'Por favor, completa todos los campos requeridos.',
            icon: 'warning',
            confirmButtonText: 'OK',
            });
            return;
        }
        form.value.user_type = 'doctor';
        router.post(route('doctors.store'), form.value, {
            onSuccess: () => {
            resetForm();
            Swal.fire({
                title: '¡Registro Exitoso!',
                text: 'El terapeuta se ha guardado correctamente.',
                icon: 'success',
                confirmButtonText: 'Asignar Terapia',
            }).then(() => {
                // router.visit(route('doctor_therapies.create')); // Removed router.visit from here
            });
            },
            onError: (errors) => {
            let errorMessage = 'Hubo un error al intentar guardar el terapeuta.';
            if (errors && typeof errors === 'object') {
                for (const key in errors) {
                const errorValue = errors[key];
                errorMessage += `<br><strong>${key}:</strong> ${
                    Array.isArray(errorValue) ? errorValue.join(', ') : errorValue
                }`;
                }
            }
            Swal.fire({
                title: 'Error',
                html: errorMessage, // Use html: instead of text:
                icon: 'error',
                confirmButtonText: 'OK',
            });
            },
        });
    };

</script>

<template>
    <Head title="Agregar Terapeuta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 ">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                <CircleX />Cancelar
            </Button>
        
            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Paso {{ currentSection }} de 2
                </div>

                <div v-if="currentSection === 1" class="space-y-4">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Datos Personales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></Label>
                            <Input
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el nombre"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</Label>
                            <Input
                                id="last_name"
                                type="text"
                                v-model="form.last_name"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el apellido"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula <span class="text-red-500">*</span></Label>
                            <Input
                                id="id_card"
                                type="text"
                                v-model="form.id_card"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="validateNumberInput"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la cédula"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el email"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Contacto <span class="text-red-500">*</span></Label>
                            <Input
                                id="phone"
                                type="text"
                                v-model="form.phone"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="validateNumberInput"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese un numero celular"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">Género</Label>
                            <select
                                id="gender"
                                v-model="form.gender"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                            >
                                <option value="" disabled>Selecciona un género</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="birth_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Nacimiento</Label>
                            <Input
                                id="birth_date"
                                type="date"
                                v-model="form.birth_date"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="age" class="text-sm font-medium text-gray-700 dark:text-gray-300">Edad</Label>
                            <Input
                                id="age"
                                type="number"
                                v-model="form.age"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la edad"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="ethnicity" class="text-sm font-medium text-gray-700 dark:text-gray-300">Etnia</Label>
                            <Input
                                id="ethnicity"
                                type="text"
                                v-model="form.ethnicity"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la etnia"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="currentSection === 2" class="space-y-4">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Formacion Academica</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
   
                        <div class="space-y-2">
                            <Label for="university_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Lugar de Formacion</Label>
                            <Input
                                id="university_name"
                                type="text"
                                v-model="form.university_name"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el lugar de formacion"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="degree_title" class="text-sm font-medium text-gray-700 dark:text-gray-300">Titulo Obtenido</Label>
                            <Input
                                id="degree_title"
                                type="text"
                                v-model="form.degree_title"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el titulo obtenido"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="graduation_year" class="text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Graduacion</Label>
                            <Input
                                id="graduation_year"
                                type="date"
                                v-model="form.graduation_year"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="speciality" class="text-sm font-medium text-gray-700 dark:text-gray-300">Especialidad</Label>
                            <Input
                                id="speciality"
                                type="text"
                                v-model="form.speciality"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese su especialidad"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="certifications" class="text-sm font-medium text-gray-700 dark:text-gray-300">Certificaciones</Label>
                            <Input
                                id="certifications"
                                type="text"
                                v-model="form.certifications"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese sus certificaciones o cursos"
                            />
                        </div>


                    </div>
                </div>

                <div class="mt-6" :class="{'flex justify-between': currentSection > 1, 'flex justify-end': currentSection === 1}">
                    <Button v-if="currentSection > 1" @click="prevSection" class="bg-gray-300 hover:bg-gray-400 text-gray-800">
                        Anterior
                    </Button>
                    <Button v-if="currentSection < 2" :disabled="!canProceed" @click="nextSection" class="bg-indigo-500 hover:bg-indigo-600 text-white">
                        Siguiente
                    </Button>
                    <Button v-if="currentSection === 2" type="submit" class="bg-green-500 hover:bg-green-600 text-white">
                        Guardar
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>