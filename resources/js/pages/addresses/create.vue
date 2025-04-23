<script setup lan="ts">
    import AppLayout from '@/Layouts/AppLayout.vue';
    import {type BreadcrumbItem, type SharedData} from '@/types';
    import {ref, onMounted, watch, defineEmits} from 'vue';
    import {Head, usePage, Link, router} from '@inertiajs/vue3';
    import { Button } from '@/Components/ui/button';
    import { Input } from '@/Components/ui/input';
    import { Label } from '@/Components/ui/label';
    import axios from 'axios';
    import { type Address } from '@/types';


    type BreadcrumbItem = {
        title: string;
        href: string;
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {title: 'Direcciones', href: '/addresses'},
        {title: 'Añadir Diereccion', href: '#'}
    ];

    const form = ref<Partial<{
        id_province: number | null;
        id_canton: number | null;
        id_parish: number | null;
        site: string | null;
        principal_street: string | null;
        secondary_street: string | null;
        reference: string | null;
    }>>({
        id_province: null,
        id_canton: null,
        id_parish: null,
        site: null,
        principal_street: null,
        secondary_street: null,
        reference: null,
    });

    const provinces = ref([]);
    const cantons = ref([]);
    const parishes = ref([]);

    const emit = defineEmits(['address-created', 'close']);

    onMounted(async () => {
        try {
            const response = await axios.get(route('api.provinces.index'));
            provinces.value = response.data;
        } catch (error) {
            console.error('Error al cargar las provincias:', error);
        }
    });

    watch(form.id_province, async (newProvinceId) => {
        if (newProvinceId) {
            try {
                const response = await axios.get(route('api.provinces.cantons', { province: newProvinceId }));
                cantons.value = response.data;
                form.value.id_canton = null;
                form.value.id_parish = null;
            } catch (error) {
                console.error('Error al cargar los cantones:', error);
            }
        } else {
            cantons.value = [];
            parishes.value = [];
            form.value.id_canton = null;
            form.value.id_parish = null;
        }
    });

    watch(form.id_canton, async (newCantonId) => {
        if (newCantonId) {
            try {
                const response = await axios.get(route('api.cantons.parishes', { canton: newCantonId })); // Usamos el nombre de la ruta
                parishes.value = response.data;
                form.value.id_parish = null;
            } catch (error) {
                console.error('Error al cargar las parroquias:', error);
            }
        } else {
            parishes.value = [];
            form.value.id_parish = null;
        }
    });

    const saveAddress = () => {
        emit('address-created', { ...form.value });
        resetForm();
        emit('close');
    };

    const resetForm = () => {
        form.value = {
            id_province: null,
            id_canton: null,
            id_parish: null,
            site: null,
            principal_street: null,
            secondary_street: null,
            reference: null,
        };
    };

    const submit = () => {
        router.post(route('addresses.store'), form.value, {
            onSuccess: () => {
                resetForm();
            },
            onError: (errors) => {
                console.error('Error al crear la dirección:', errors);
            }
        });
    };
</script>

<template>

<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h2 id="modal-title" class="text-lg font-bold mb-4">Crear Nueva Dirección</h2>
            <div class="space-y-4">
                <div>
                    <Label for="id_province" class="block text-gray-700 text-sm font-bold mb-2">Provincia</Label>
                    <Select id="id_province" v-model="form.id_province">
                        <option value="" disabled>Seleccionar Provincia</option>
                        <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
                    </Select>
                </div>

                <div>
                    <Label for="id_canton" class="block text-gray-700 text-sm font-bold mb-2">Cantón</Label>
                    <Select id="id_canton" v-model="form.id_canton" :disabled="!form.id_province">
                        <option value="" disabled>Seleccionar Cantón</option>
                        <option v-for="canton in cantons" :key="canton.id" :value="canton.id">{{ canton.name }}</option>
                    </Select>
                </div>

                <div>
                    <Label for="id_parish" class="block text-gray-700 text-sm font-bold mb-2">Parroquia</Label>
                    <Select id="id_parish" v-model="form.id_parish" :disabled="!form.id_canton">
                        <option value="" disabled>Seleccionar Parroquia</option>
                        <option v-for="parish in parishes" :key="parish.id" :value="parish.id">{{ parish.name }}</option>
                    </Select>
                </div>

                <div>
                    <Label for="site" class="block text-gray-700 text-sm font-bold mb-2">Sitio</Label>
                    <Input type="text" id="site" v-model="form.site" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Barrio, Urbanización" />
                </div>
                <div>
                    <Label for="principal_street" class="block text-gray-700 text-sm font-bold mb-2">Calle Principal</Label>
                    <Input type="text" id="principal_street" v-model="form.principal_street" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Av. Principal" required />
                </div>
                <div>
                    <Label for="secondary_street" class="block text-gray-700 text-sm font-bold mb-2">Calle Secundaria</Label>
                    <Input type="text" id="secondary_street" v-model="form.secondary_street" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Calle Secundaria" />
                </div>
                <div>
                    <Label for="reference" class="block text-gray-700 text-sm font-bold mb-2">Referencia</Label>
                    <Input type="text" id="reference" v-model="form.reference" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Cerca del parque" />
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="$emit('close')">Cancelar</Button>
                    <Button type="button" class="bg-indigo-500 text-white hover:bg-indigo-700" @click="saveAddress">Guardar Dirección</Button>
                </div>
            </div>
        </div>
    </div>

</template>