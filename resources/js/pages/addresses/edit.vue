<script setup lang="ts">
import { ref, computed, watch, reactive } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Address, User } from '@/types';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CircleX } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { PageProps } from '@inertiajs/core';

const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const userStatus = props.user.status;
    return [
        {
            title: userStatus === 'paciente' ? 'Pacientes' : 'Aspirantes',
            href: userStatus === 'paciente' ? route('patients.index') : route('users.index')
        },
        { title: 'Editar Dirección', href: '#' },
    ];
});

interface EditPageProps extends PageProps {
  address: Address;
  user: User;
  users: User[];
  provinces: { id: number; name_province: string }[];
  cantons: { id: number; name_canton: string; id_province: number }[];
  parishes: { id: number; parroquia: string; id_canton: number }[];
}

const { props } = usePage<EditPageProps>();

const form = reactive({
  id: props.address.id,
  id_user: props.address.id_user,
  id_province: props.address.id_province,
  id_canton: props.address.id_canton,
  id_parish: props.address.id_parish,
  site: props.address.site,
  principal_street: props.address.principal_street,
  secondary_street: props.address.secondary_street,
  reference: props.address.reference,
});

const filteredCantons = computed(() =>
  props.cantons.filter(c => c.id_province === form.id_province)
);
const filteredParishes = computed(() =>
  props.parishes.filter(p => p.id_canton === form.id_canton)
);

watch(() => form.id_province, () => {
  form.id_canton = null;
  form.id_parish = null;
});
watch(() => form.id_canton, () => {
  form.id_parish = null;
});

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
            if (props.user.status === 'paciente') {
                router.visit(route('patients.index'));
            } else {
                router.visit(route('users.index'));
            }
        }
    });
};

const submit = () => {
  router.put(route('addresses.update', { address: form.id }), form, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Éxito', 'Dirección actualizada correctamente.', 'success');
        // Redirigir según el estado del usuario después de una actualización exitosa
        if (props.user.status === 'paciente') {
            router.visit(route('patients.index')); // Si es paciente, va a patient.index
        } else {
            router.visit(route('users.index')); // Si no, va a users.index
        }
    },
    onError: (errors) => {
      let msg = 'Errores encontrados:<br>';
      for (const key in errors) {
        msg += `<strong>${key}:</strong> ${errors[key]}<br>`;
      }
      Swal.fire('Error', msg, 'error');
    },
  });
};
</script>

<template>
    <Head title="Editar Dirección" />
    <AppLayout :breadcrumbs="breadcrumbs">
  
      <div class="p-6">

        <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
          <CircleX />Cancelar
        </Button>
  
        <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.95;">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Datos Domiciliarios</h2>
  
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Paciente</Label>
                  <p class="text-white bg-gray-700 p-2 rounded">
                    {{ props.user.name }} {{ props.user.last_name }}
                  </p>
            </div>
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Provincia</Label>
              <select v-model="form.id_province" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2">
                <option :value="null" disabled>Seleccione una provincia</option>
                <option v-for="province in props.provinces" :key="province.id" :value="province.id">
                  {{ province.name_province }}
                </option>
              </select>
            </div>
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Cantón</Label>
              <select v-model="form.id_canton" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" :disabled="!form.id_province">
                <option :value="null" disabled>Seleccione un cantón</option>
                <option v-for="canton in filteredCantons" :key="canton.id" :value="canton.id">
                  {{ canton.name_canton }}
                </option>
              </select>
            </div>
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Parroquia</Label>
              <select v-model="form.id_parish" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" :disabled="!form.id_canton">
                <option :value="null" disabled>Seleccione una parroquia</option>
                <option v-for="parish in filteredParishes" :key="parish.id" :value="parish.id">
                  {{ parish.parroquia }}
                </option>
              </select>
            </div>
  
            <div class="space-y-2 md:col-span-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sitio</Label>
              <Input v-model="form.site" type="text" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" placeholder="Ej: Comunidad San José" />
            </div>
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Calle Principal</Label>
              <Input v-model="form.principal_street" type="text" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" />
            </div>
  
            <div class="space-y-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Calle Secundaria</Label>
              <Input v-model="form.secondary_street" type="text" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" />
            </div>
  
            <div class="space-y-2 md:col-span-2">
              <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Referencia</Label>
              <Input v-model="form.reference" type="text" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded p-2" />
            </div>
  
          </div>
  
          <div class="mt-6">
            <Button type="submit" class="bg-green-500 hover:bg-green-600 text-white">
              Actualizar Dirección
            </Button>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>
