<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import type { Appointment, User, Therapy } from '@/types';

const props = defineProps<{
  appointment: Appointment;
  doctors: User[];
  therapies: Therapy[];
  weekdays: string[];
}>();

const form = useForm({
  id_doctor: props.appointment.id_doctor,
  id_therapy: props.appointment.id_therapy,
  day: props.appointment.day,
  start_time: props.appointment.start_time,
  end_time: props.appointment.end_time,
});

const selectDay = (day: string) => {
  form.day = day;
};

const breadcrumbs = [
   { title: 'Editar horarios', href: '#' }
];

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
            router.visit(route('appointments.index'));
        }
    });
};


const submit = async () => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción actualizará el horario seleccionado.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, actualizar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#d33',
  })

  if (result.isConfirmed) {
    form.put(route('appointments.update', props.appointment.id), {
      onSuccess: () => {
        Swal.fire({
          title: 'Actualizado',
          text: 'El horario fue actualizado correctamente.',
          icon: 'success',
          confirmButtonColor: '#2563eb',
        })
      },
      onError: () => {
        Swal.fire({
          title: 'Error',
          text: 'Ocurrió un error al actualizar el horario.',
          icon: 'error',
          confirmButtonColor: '#d33',
        })
      }
    })
  }
}
</script>

<template>
    <Head title="Editar Carga Horaria" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                    <CircleX />Cancelar
            </Button>

            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">
            
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Doctor:</Label>
                        <select v-model="form.id_doctor" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"  disabled>
                            <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                            {{ doc.name }} {{ doc.last_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Terapia:</Label>
                        <select v-model="form.id_therapy" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"  disabled>
                            <option v-for="therapy in therapies" :key="therapy.id" :value="therapy.id">
                            {{ therapy.name }}
                            </option>
                        </select>
                    </div>
                </div>

                
                <div class="space-y-2">
                <Label for="day" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Día disponible
                </Label>

                <select
                    id="day"
                    v-model="form.day"
                    class="hidden"
                >
                    <option
                    v-for="day in weekdays"
                    :key="day"
                    :value="day"
                    >
                    {{ day }}
                    </option>
                </select>

                
                <div class="w-180 flex flex-wrap gap-2 p-2 border-gray-300 dark:border-gray-600 rounded-md">
                    <template v-for="day in weekdays" :key="day">
                    <button
                        type="button"
                        @click="selectDay(day)"
                        :class="[
                        'inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium transition-all',
                        form.day === day
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-200 text-gray-800 dark:bg-indigo-400 dark:text-gray-700'
                        ]"
                    >
                        {{ day }}
                    </button>
                    </template>
                </div>
                </div>

                <div>
                    <Label>Hora Inicio:</Label>
                    <input type="time" v-model="form.start_time" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required/>
                </div>

                <div>
                    <Label>Hora Fin:</Label>
                    <input type="time" v-model="form.end_time" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required/>
                </div>

            <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white">Actualizar</Button>
        </form>


        </div>
       
    </AppLayout>
</template>
