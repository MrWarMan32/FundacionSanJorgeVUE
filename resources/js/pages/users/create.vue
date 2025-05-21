<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {type BreadcrumbItem} from '@/types';
    import {computed, ref, watch} from 'vue'; // Import watch for reactivity
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
    });

    
    const errors = ref<Record<string, string>>({});

    
    watch(() => form.value.disable_card, (newValue) => {
        if (!newValue) {
            form.value.id_disable_card = ''; 
            delete errors.value.id_disable_card; 
        }
    });

    watch(() => form.value.birth_date, (newBirthDate) => {
        if (newBirthDate) {
            const birthDateObj = new Date(newBirthDate);
            const today = new Date();
            let calculatedAge = today.getFullYear() - birthDateObj.getFullYear();
            const m = today.getMonth() - birthDateObj.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDateObj.getDate())) {
                calculatedAge--;
            }
            form.value.age = calculatedAge;
            delete errors.value.age;
        } else {
            form.value.age = undefined; 
        }
    });

    const validateNumberInput = (event: Event, field: 'id_card' | 'representative_id_card' | 'id_disable_card') => {
        const inputElement = event.target as HTMLInputElement;
        const numericValue = inputElement.value.replace(/[^0-9]/g, '');
        if (field === 'id_card') {
            form.value.id_card = numericValue;
        } else if (field === 'representative_id_card') {
            form.value.representative_id_card = numericValue;
        } else if (field === 'id_disable_card') {
            form.value.id_disable_card = numericValue;
        }
        if (errors.value[field] && numericValue) {
            delete errors.value[field];
        }
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

    const canProceedClientSide = computed(() => {
        if (currentSection.value === 1) {
            return form.value.name && form.value.last_name && form.value.id_card && form.value.email && form.value.gender && form.value.birth_date && form.value.ethnicity;
        }
        if (currentSection.value === 2) {
            const isIdDisableCardValid = form.value.disable_card ? !!form.value.id_disable_card : true;
            return isIdDisableCardValid && (form.value.disability_type && form.value.disability_type.length > 0) && form.value.disability_level && form.value.disability_grade !== undefined && form.value.cause_disability && form.value.diagnosis;
        }
        if (currentSection.value === 3) {
            return form.value.representative_name && form.value.representative_last_name && form.value.representative_id_card && form.value.phone;
        }
        return true;
    });

    const nextSection = () => {
        if (canProceedClientSide.value) {
            currentSection.value++;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Campos Incompletos',
                text: 'Por favor, completa todos los campos obligatorios en esta sección antes de continuar.',
                confirmButtonText: 'Entendido'
            });
        }
    };

    const prevSection = () => {
        currentSection.value--;
        errors.value = {}; 
    };

    const resetForm = () => {
        Object.keys(form.value).forEach(key => {
            form.value[key as keyof typeof form.value] = undefined;
        });
        form.value.disable_card = false;
        form.value.user_type = 'usuario';
        form.value.status = 'aspirante';
        form.value.disability_type = [];
        errors.value = {};
        currentSection.value = 1; 
    };
    
    const toggleOption = (option: string) => {
        if (!form.value.disability_type) {
            form.value.disability_type = []
        }

        const index = form.value.disability_type.indexOf(option)
        if (index > -1) {
            form.value.disability_type.splice(index, 1)
        } else {
            form.value.disability_type.push(option)
        }
        if (errors.value.disability_type && form.value.disability_type.length > 0) {
            delete errors.value.disability_type;
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
            onError: (serverErrors) => {
                console.error('Error al crear el aspirante:', serverErrors);
                errors.value = serverErrors; 
                if (Object.keys(serverErrors).length > 0) {
                    let firstErrorSection = 1;
                    if (serverErrors.name || serverErrors.last_name || serverErrors.id_card || serverErrors.email || serverErrors.gender || serverErrors.birth_date || serverErrors.age || serverErrors.ethnicity) {
                        firstErrorSection = 1;
                    } else if (serverErrors.disable_card || serverErrors.id_disable_card || serverErrors.disability_type || serverErrors['disability_type.'] || serverErrors.disability_level || serverErrors.disability_grade || serverErrors.cause_disability || serverErrors.diagnosis) {
                        firstErrorSection = 2;
                    } else if (serverErrors.representative_name || serverErrors.representative_last_name || serverErrors.representative_id_card || serverErrors.phone) {
                        firstErrorSection = 3;
                    }
                    currentSection.value = firstErrorSection;

                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: 'Se encontraron errores. Por favor, revisa los campos resaltados.',
                        confirmButtonText: 'Entendido'
                    });
                }
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
                                :class="{'border-red-500': errors.name}"
                                @input="delete errors.name"
                            />
                            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido <span class="text-red-500">*</span></Label>
                            <Input
                                id="last_name"
                                type="text"
                                v-model="form.last_name"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el apellido"
                                :class="{'border-red-500': errors.last_name}"
                                @input="delete errors.last_name"
                            />
                            <p v-if="errors.last_name" class="text-red-500 text-xs mt-1">{{ errors.last_name }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula <span class="text-red-500">*</span></Label>
                            <Input
                                id="id_card"
                                type="text"
                                v-model="form.id_card"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="validateNumberInput($event, 'id_card')"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la cédula"
                                :class="{'border-red-500': errors.id_card}"
                            />
                            <p v-if="errors.id_card" class="text-red-500 text-xs mt-1">{{ errors.id_card }}</p>
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
                                :class="{'border-red-500': errors.email}"
                                @input="delete errors.email"
                            />
                            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">Género <span class="text-red-500">*</span></Label>
                            <select
                                id="gender"
                                v-model="form.gender"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                                :class="{'border-red-500': errors.gender}"
                                @change="delete errors.gender"
                            >
                                <option value="" disabled>Selecciona un género</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                            <p v-if="errors.gender" class="text-red-500 text-xs mt-1">{{ errors.gender }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="birth_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Nacimiento <span class="text-red-500">*</span></Label>
                            <Input
                                id="birth_date"
                                type="date"
                                v-model="form.birth_date"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{'border-red-500': errors.birth_date}"
                                @input="delete errors.birth_date"
                            />
                            <p v-if="errors.birth_date" class="text-red-500 text-xs mt-1">{{ errors.birth_date }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="age" class="text-sm font-medium text-gray-700 dark:text-gray-300">Edad <span class="text-red-500">*</span></Label>
                            <Input
                                id="age"
                                type="number"
                                v-model="form.age"
                                required
                                disabled
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la edad"
                                :class="{'border-red-500': errors.age}"
                                @input="delete errors.age"
                            />
                            <p v-if="errors.age" class="text-red-500 text-xs mt-1">{{ errors.age }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="ethnicity" class="text-sm font-medium text-gray-700 dark:text-gray-300">Etnia <span class="text-red-500">*</span></Label>
                            <select
                                id="ethnicity"
                                v-model="form.ethnicity"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                                :class="{'border-red-500': errors.ethnicity}"
                                @change="delete errors.ethnicity"
                            >
                                <option value="" disabled>Selecciona una etnia</option>
                                <option value="mestizo">Mestizo</option>
                                <option value="indigena">Indígena</option>
                                <option value="afrodescendiente">Afrodescendiente</option>
                                <option value="blanco">Blanco</option>
                                <option value="montubio">Montubio</option>
                                <option value="asiatico">Asiático</option>
                                <option value="prefiero_no_decir">Prefiero no decir</option>
                            </select>
                            <p v-if="errors.ethnicity" class="text-red-500 text-xs mt-1">{{ errors.ethnicity }}</p>
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
                            <Label for="id_disable_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Número de Carnet de Discapacidad <span v-if="form.disable_card" class="text-red-500">*</span></Label>
                                <Input
                                    id="id_disable_card"
                                    type="text"
                                    v-model="form.id_disable_card"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    @input="validateNumberInput($event, 'id_disable_card')"
                                    :required="form.disable_card"
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ingrese el número de carnet"
                                    :disabled="!form.disable_card"
                                    :class="{'border-red-500': errors.id_disable_card}"
                                />
                                <p v-if="errors.id_disable_card" class="text-red-500 text-xs mt-1">{{ errors.id_disable_card }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="disability_level" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nivel de Discapacidad <span class="text-red-500">*</span></Label>
                            <Input
                                id="disability_level"
                                type="text"
                                v-model="form.disability_level"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el nivel de discapacidad"
                                :class="{'border-red-500': errors.disability_level}"
                                @input="delete errors.disability_level"
                            />
                            <p v-if="errors.disability_level" class="text-red-500 text-xs mt-1">{{ errors.disability_level }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="disability_grade" class="text-sm font-medium text-gray-700 dark:text-gray-300">Grado de Discapacidad (%) <span class="text-red-500">*</span></Label>
                            <Input
                                id="disability_grade"
                                type="number"
                                v-model="form.disability_grade"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el grado de discapacidad"
                                :class="{'border-red-500': errors.disability_grade}"
                                @input="delete errors.disability_grade"
                            />
                            <p v-if="errors.disability_grade" class="text-red-500 text-xs mt-1">{{ errors.disability_grade }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="cause_disability" class="text-sm font-medium text-gray-700 dark:text-gray-300">Causa de Discapacidad <span class="text-red-500">*</span></Label>
                            <Input
                                id="cause_disability"
                                type="text"
                                v-model="form.cause_disability"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la causa de la discapacidad"
                                :class="{'border-red-500': errors.cause_disability}"
                                @input="delete errors.cause_disability"
                            />
                            <p v-if="errors.cause_disability" class="text-red-500 text-xs mt-1">{{ errors.cause_disability }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="diagnosis" class="text-sm font-medium text-gray-700 dark:text-gray-300">Diagnóstico <span class="text-red-500">*</span></Label>
                            <Input
                                id="diagnosis"
                                type="text"
                                v-model="form.diagnosis"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el diagnóstico"
                                :class="{'border-red-500': errors.diagnosis}"
                                @input="delete errors.diagnosis"
                            />
                            <p v-if="errors.diagnosis" class="text-red-500 text-xs mt-1">{{ errors.diagnosis }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="disability_type" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipos de Discapacidad (seleccione uno o varios) <span class="text-red-500">*</span>
                            </label>

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

                            <div class="w-180 flex flex-wrap gap-2 p-2 rounded-md"
                                :class="{'border-red-500': errors.disability_type || errors['disability_type.0'], 'border-gray-300 dark:border-gray-600': !(errors.disability_type || errors['disability_type.0'])}"
                            >
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
                            <p v-if="errors.disability_type || errors['disability_type.0']" class="text-red-500 text-xs mt-1">
                                {{ errors.disability_type || errors['disability_type.0'] }}
                            </p>
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
                                :class="{'border-red-500': errors.representative_name}"
                                @input="delete errors.representative_name"
                            />
                            <p v-if="errors.representative_name" class="text-red-500 text-xs mt-1">{{ errors.representative_name }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="representative_last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido del Representante <span class="text-red-500">*</span></Label>
                            <Input
                                id="representative_last_name"
                                type="text"
                                v-model="form.representative_last_name"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el apellido del representante"
                                :class="{'border-red-500': errors.representative_last_name}"
                                @input="delete errors.representative_last_name"
                            />
                            <p v-if="errors.representative_last_name" class="text-red-500 text-xs mt-1">{{ errors.representative_last_name }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="representative_id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula del Representante <span class="text-red-500">*</span></Label>
                            <Input
                                id="representative_id_card"
                                type="text"
                                v-model="form.representative_id_card"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="validateNumberInput($event, 'representative_id_card')"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese la cédula del representante"
                                :class="{'border-red-500': errors.representative_id_card}"
                            />
                            <p v-if="errors.representative_id_card" class="text-red-500 text-xs mt-1">{{ errors.representative_id_card }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono <span class="text-red-500">*</span></Label>
                            <Input
                                id="phone"
                                type="text"
                                v-model="form.phone"
                                required
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ingrese el teléfono"
                                :class="{'border-red-500': errors.phone}"
                                @input="delete errors.phone"
                            />
                            <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6" :class="{'flex justify-between': currentSection > 1, 'flex justify-end': currentSection === 1}">
                    <Button v-if="currentSection > 1" @click="prevSection" type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800">
                        Anterior
                    </Button>
                    <Button v-if="currentSection < 3" :disabled="!canProceedClientSide" @click="nextSection" type="button" class="bg-indigo-500 hover:bg-indigo-600 text-white">
                        Siguiente
                    </Button>
                    <Button v-if="currentSection === 3" type="submit" class="bg-green-500 hover:bg-green-600 text-white">
                        Guardar y Añadir dirección
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>