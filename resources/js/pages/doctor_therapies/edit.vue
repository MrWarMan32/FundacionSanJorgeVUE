<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import Swal from 'sweetalert2';
import { Therapy, User, type BreadcrumbItem } from '@/types';
import { CircleX } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  {title: 'Terapeutas', href: '/doctor_therapies'},
  { title: 'Cambiar Asignacion', href: '#' },
];

const props = defineProps({
  doctor_therapies: {
    type: Object as () => {
      id: number,
      id_doctor: number,
      id_therapy: number
    },
    required: true,
  },
  doctors: {
    type: Array as () => User[],
    required: true,
  },
  therapies: {
    type: Array as () => Therapy[],
    required: true,
  },
});

const form = useForm({
  id_doctor: props.doctor_therapies?.id_doctor??'',
  id_therapy: props.doctor_therapies?.id_therapy??'',
});

function submit() {
  Swal.fire({
    title: '¿Guardar cambios?',
    text: '¿Estás seguro de que deseas actualizar esta asignación?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      form.put(route('doctor_therapies.update', props.doctor_therapies.id), {
        onSuccess: () => {
          Swal.fire({
            title: 'Actualizado',
            text: 'La asignación fue actualizada correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar',
          }).then(() => {
            router.visit(route('doctor_therapies.index'));
          });
        },
        onError: () => {
          Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al actualizar la asignación.',
            icon: 'error',
            confirmButtonText: 'Cerrar',
          });
        },
      });
    }
  });
}

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
      router.visit(route('doctor_therapies.index'));
    }
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Editar Asignación de Terapia" />

    <div class="p-6">
      <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
        <CircleX /> Cancelar
      </Button>

      <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Editar asignación</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terapeuta</Label>
            <select
              v-model="form.id_doctor"
              class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
            >
              <option value="">Seleccione un doctor</option>
              <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                {{ doctor.name }} {{ doctor.last_name }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terapia</Label>
            <select
              v-model="form.id_therapy"
              class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
            >
              <option disabled value="">Seleccione una terapia</option>
              <option v-for="therapy in therapies" :key="therapy.id" :value="therapy.id">
                {{ therapy.name }}
              </option>
            </select>
          </div>
        </div>

        <div>
          <Button type="submit" class="bg-green-500 hover:bg-green-700 text-white right-100">Guardar Cambios</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
