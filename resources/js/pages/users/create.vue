<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {type BreadcrumbItem, type SharedData} from '@/types';
    import {ref} from 'vue';
    import {Head, usePage, Link, router} from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    
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
        'Sensorial (Visual)',
        'Sensorial (Auditiva)',
        'Psiquica',
        'Visceral',
        'Psicocognitiva',
        'Psicosocial',
        'Multiple',
    ]);

    const currentSection = ref(1);
    const errorsSection1 = ref<string[]>([]);
    const errorsSection2 = ref<string[]>([]);
    const errorsSection3 = ref<string[]>([]);

    const nextSection = () => {
        currentSection.value++;
    };

    const prevSection = () => {
        currentSection.value--;
    };

    const resetForm = () => {
        Object.keys(form.value).forEach(key => {
            form.value[key as keyof typeof form.value] = undefined;
        });
        form.value.disable_card = false;
        form.value.user_type = 'usuario';
        form.value.status = 'aspirante';
        //form.value.address = null; // Resetea también la dirección
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
                resetForm();
            },
            onError: (errors) => {
                console.error('Error al crear el aspirante:', errors);
            },
        });
    };

</script>

<template>
    <Head title="Agregar Aspirante"/>
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
            <h1 class="text-2xl font-bold">Agregar Aspirante</h1>

            <form @submit.prevent="submit" class="max-w-lg space-y-6">
                <div v-if="currentSection === 1">
                    <h2 class="text-xl font-semibold mb-4">Datos Personales</h2>
                    <div class="flex flex-col gap-2">
                        <Label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</Label>
                        <Input id="name" type="text" v-model="form.name" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</Label>
                        <Input id="last_name" type="text" v-model="form.last_name" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula</Label>
                        <Input id="id_card" type="text" v-model="form.id_card" inputmode="numeric" pattern="[0-9]*" @input="validateNumberInput" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</Label>
                        <Input id="email" type="text" v-model="form.email" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">Género</Label>
                        <Input id="gender" type="text" v-model="form.gender" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="birth_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Nacimiento</Label>
                        <Input id="birth_date" type="date" v-model="form.birth_date" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="age" class="text-sm font-medium text-gray-700 dark:text-gray-300">Edad</Label>
                        <Input id="age" type="number" v-model="form.age" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="ethnicity" class="text-sm font-medium text-gray-700 dark:text-gray-300">Etnia</Label>
                        <Input id="ethnicity" type="text" v-model="form.ethnicity" />
                    </div>
                    <div class="flex justify-end">
                        <Button type="button" @click="nextSection">Siguiente</Button>
                    </div>
                </div>

                <div v-if="currentSection === 2">
                    <h2 class="text-xl font-semibold mb-4">Datos de Discapacidad</h2>

                    <div class="flex flex-col gap-2">
                        <Label for="disable_card" class="inline-flex items-center">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">¿Tiene Carnet de Discapacidad?</span>
                            <select
                                id="disable_card"
                                v-model="form.disable_card"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-indigo-400 dark:focus:ring-indigo-600"
                            >
                                <option value="true">Sí</option>
                                <option value="false">No</option>

                            </select>
                        </Label>
                    </div>

                    <!-- Mostrar el campo de Carnet de Discapacidad solo si 'Sí' está seleccionado -->
                    <div class="flex flex-col gap-2" v-if="form.disable_card === 'true'">
                        <Label for="id_disable_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Número de Carnet de Discapacidad</Label>
                        <Input id="id_disable_card" type="number" v-model="form.id_disable_card" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label for="disability_type" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tipos de Discapacidad (seleccione uno o varios)
                        </Label>
                        <select
                            id="disability_type"
                            multiple
                            v-model="form.disability_type"
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:ring-indigo-600"
                        >
                            <option v-for="option in disabilityOptions" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>

                        <!-- Resumen de opciones seleccionadas -->
                        <div v-if="form.disability_type && form.disability_type.length" class="mt-2">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Seleccionado(s):</span>
                            <ul class="list-disc list-inside text-sm text-gray-800 dark:text-white">
                                <li v-for="(item, index) in form.disability_type" :key="index">{{ item }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label for="disability_level" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nivel de Discapacidad</Label>
                        <Input id="disability_level" type="text" v-model="form.disability_level" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="disability_grade" class="text-sm font-medium text-gray-700 dark:text-gray-300">Grado de Discapacidad (%)</Label>
                        <Input id="disability_grade" type="number" v-model="form.disability_grade" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="cause_disability" class="text-sm font-medium text-gray-700 dark:text-gray-300">Causa de Discapacidad</Label>
                        <Input id="cause_disability" type="text" v-model="form.cause_disability" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="diagnosis" class="text-sm font-medium text-gray-700 dark:text-gray-300">Diagnóstico</Label>
                        <Input id="diagnosis" type="text" v-model="form.diagnosis" />
                    </div>
                    <div class="flex justify-between">
                        <Button type="button" @click="prevSection">Anterior</Button>
                        <Button type="button" @click="nextSection">Siguiente</Button>
                    </div>
                </div>

                <div v-if="currentSection === 3">
                    <h2 class="text-xl font-semibold mb-4">Datos del Representante</h2>
                    <div class="flex flex-col gap-2">
                        <Label for="representative_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Representante</Label>
                        <Input id="representative_name" type="text" v-model="form.representative_name" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="representative_last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellido del Representante</Label>
                        <Input id="representative_last_name" type="text" v-model="form.representative_last_name" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="representative_id_card" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cédula del Representante</Label>
                        <Input id="representative_id_card" type="number" v-model="form.representative_id_card" inputmode="numeric" pattern="[0-9]*" @input="validateNumberInput" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</Label>
                        <Input id="phone" type="text" v-model="form.phone" />
                    </div>
                    <div class="flex justify-between">
                        <Button type="button" @click="prevSection">Anterior</Button>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" class="bg-indigo-500 text-white hover:bg-indigo-700">
                            Crear Aspirante
                        </Button>
                        <Button as="a" href="/users" variant="outline">Cancelar</Button>
                    </div>
                    
                </div>
            </form>
        </div>
    </AppLayout>
</template>