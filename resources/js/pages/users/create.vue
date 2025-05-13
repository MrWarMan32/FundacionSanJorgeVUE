<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {type BreadcrumbItem} from '@/types';
    import {computed, ref} from 'vue';
    import {Head, router} from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import {CircleX} from 'lucide-vue-next';
    import Swal from 'sweetalert2';


    const breadcrumbs: BreadcrumbItem[] = [
        {title: 'Aspirantes', href: '/users'},
        {title: 'Agregar Aspirante', href: '#'},
    ];

    const form = ref<Partial<{
        name: string;
        last_name: string;
        id_card: string;
        gender: string;
        birth_date: string;
        age: number;
        ethnicity: string;
        disable_card: boolean;
        id_disable_card: string;
        representative_name: string;
        representative_last_name: string;
        representative_id_card: string;
        phone: string;
        disability_type: string[];
        disability_level: string;
        disability_grade: number;
        cause_disability: string;
        diagnosis: string;
        email: string;
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
        disable_card: false,
        id_disable_card: '',
        representative_name: '',
        representative_last_name: '',
        representative_id_card: '',
        phone: '',
        disability_type: [],
        disability_level: '',
        disability_grade: undefined,
        cause_disability: '',
        diagnosis: '',
        email:'',
        user_type: 'usuario',
        status: 'aspirante',
        //id_address: null, // Inicializado como null
    });

   
    const validateNumberInput = (event: Event) => {
        const inputElement = event.target as HTMLInputElement;
        inputElement.value = inputElement.value.replace(/[^0-9]/g, '');
        // Actualiza el valor del v-model manualmente
        form.value.id_card = inputElement.value;
    };


    const disabilityOptions = ref([
        'Fisica',
        'Intelectual',
        'Visual',
        'Auditiva',
        'Psiquica',
        'Visceral',
        'Psicocognitiva',
        'Psicosocial',
        'Multiple',
    ]);

    const currentSection = ref(1);

    const nextSection = () => {
        currentSection.value++;
    };

    const prevSection = () => {
        currentSection.value--;
    };

    const canProceed = computed(() => {
        if (currentSection.value === 1) {
            return form.value.name && form.value.last_name && form.value.id_card && form.value.email && form.value.gender && form.value.birth_date && form.value.age && form.value.ethnicity;
        }
        if (currentSection.value === 2) {
            return form.value.disability_type && form.value.disability_level && form.value.disability_grade && form.value.cause_disability && form.value.diagnosis;
        }
        if (currentSection.value === 3) {
            return form.value.representative_name && form.value.representative_last_name && form.value.representative_id_card && form.value.phone;
        }
        return true;
    });

    const resetForm = () => {
        Object.keys(form.value).forEach(key => {
            form.value[key as keyof typeof form.value] = undefined;
        });
        form.value.disable_card = false;
        form.value.user_type = 'usuario';
        form.value.status = 'aspirante';
        //form.value.address = null; // Resetea también la dirección
    };

    
    const toggleOption = (option: string) => {
        // Si disability_type está indefinido, inicialízalo como arreglo vacío
        if (!form.value.disability_type) {
            form.value.disability_type = []
        }

        const index = form.value.disability_type.indexOf(option)
        if (index > -1) {
            form.value.disability_type.splice(index, 1)
        } else {
            form.value.disability_type.push(option)
        }
    };

    const cancel = () => {
        Swal.fire({
            title: '¿Cancelar?',
            text: '¿Estás seguro de que deseas cancelar? Se perderán los cambios no guardados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, volver',
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('users.index'));
            }
        });
    };


    const submit = () => {
        const formData = new FormData();

        for (const key in form.value) {
            const value = form.value[key as keyof typeof form.value];

            if (key === 'disable_card') {
                formData.append(key, value ? '1' : '0');
            } else if (key === 'disability_type' && Array.isArray(value)) {
                value.forEach((v, i) => {
                    formData.append(`disability_type[${i}]`, v);
                });
            } else {
                if (value !== null && value !== undefined) {
                    formData.append(key, value as any);
                }
            }
        }

        router.post(route('users.store'), formData, {
            forceFormData: true,
            onSuccess: () => {
                Swal.fire({
                title: '¡Aspirante creado!',
                text: 'El aspirante se guardó correctamente.',
                icon: 'success',
                confirmButtonText: 'Añadir dirección'
            }).then(() => {
                resetForm();
            });
            },
            onError: (errors) => {
                console.error('Error al crear el aspirante:', errors);
            },
        });
    };

</script>

<template>
    <Head title="Agregar Aspirante" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 ">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                <CircleX />Cancelar
            </Button>
        
            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Paso {{ currentSection }} de 3 
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
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Datos de Discapacidad</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="space-y-2">
                            <Label for="disable_card_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                ¿Tiene Carnet de Discapacidad?
                            </Label>
                            <select
                                id="disable_card_select"
                                v-model="form.disable_card"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                            >
                                <option :value="true" class="dark:bg-gray-700 dark:text-white">Sí</option>
                                <option :value="false" class="dark:bg-gray-700 dark:text-white">No</option>

                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="id_disable_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Número de Carnet de Discapacidad</Label>
                                <Input
                                    id="id_disable_card"
                                    type="number"
                                    required
                                    v-model="form.id_disable_card"
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ingrese el número de carnet"
                                    :disabled="!form.disable_card"
                                />
                        </div>

                        <div class="space-y-2">
                            <Label for="disability_level" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nivel de Discapacidad</Label>
                            <Input
                                id="disability_level"
                                type="text"
                                v-model="form.disability_level"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el nivel de discapacidad"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="disability_grade" class="text-sm font-medium text-gray-700 dark:text-gray-300">Grado de Discapacidad (%)</Label>
                            <Input
                                id="disability_grade"
                                type="number"
                                v-model="form.disability_grade"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el grado de discapacidad"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="cause_disability" class="text-sm font-medium text-gray-700 dark:text-gray-300">Causa de Discapacidad</Label>
                            <Input
                                id="cause_disability"
                                type="text"
                                v-model="form.cause_disability"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la causa de la discapacidad"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="diagnosis" class="text-sm font-medium text-gray-700 dark:text-gray-300">Diagnóstico</Label>
                            <Input
                                id="diagnosis"
                                type="text"
                                v-model="form.diagnosis"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el diagnóstico"
                            />
                        </div>

                        <div class="space-y-2">
                            <label for="disability_type" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipos de Discapacidad (seleccione uno o varios)
                            </label>

                            <!-- Select oculto para manejar selección -->
                            <select
                                id="disability_type"
                                v-model="form.disability_type"
                                multiple
                                class="hidden"
                            >
                                <option
                                    v-for="option in disabilityOptions"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>

                            <!-- Contenedor visual w-[21.75rem]-->
                            <div class="w-180 flex flex-wrap gap-2 p-2 border-gray-300 dark:border-gray-600 rounded-md">
                                <template v-for="option in disabilityOptions" :key="option">
                                    <button
                                        type="button"
                                        @click="toggleOption(option)"
                                        :class="[
                                            'inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium',
                                            (form.disability_type ?? []).includes(option)
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-gray-200 text-gray-800 dark:bg-indigo-400 dark:text-gray-700'
                                        ]"
                                    >
                                        {{ option }}
                                    </button>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <div v-if="currentSection === 3" class="space-y-4">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Datos del Representante</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="representative_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Representante <span class="text-red-500">*</span></Label>
                            <Input
                                id="representative_name"
                                type="text"
                                v-model="form.representative_name"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el nombre del representante"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="representative_last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido del Representante</Label>
                            <Input
                                id="representative_last_name"
                                type="text"
                                v-model="form.representative_last_name"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el apellido del representante"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="representative_id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula del Representante <span class="text-red-500">*</span></Label>
                            <Input
                                id="representative_id_card"
                                type="number"
                                v-model="form.representative_id_card"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="validateNumberInput"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la cédula del representante"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</Label>
                            <Input
                                id="phone"
                                type="text"
                                v-model="form.phone"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el teléfono"
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-6" :class="{'flex justify-between': currentSection > 1, 'flex justify-end': currentSection === 1}">
                    <Button v-if="currentSection > 1" @click="prevSection" class="bg-gray-300 hover:bg-gray-400 text-gray-800">
                        Anterior
                    </Button>
                    <Button v-if="currentSection < 3" :disabled="!canProceed" @click="nextSection" class="bg-indigo-500 hover:bg-indigo-600 text-white">
                        Siguiente
                    </Button>
                    <Button v-if="currentSection === 3" type="submit" class="bg-green-500 hover:bg-green-600 text-white">
                        Guardar y Añadir direccion
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>