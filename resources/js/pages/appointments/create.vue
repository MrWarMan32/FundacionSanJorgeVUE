<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Swal from 'sweetalert2';
import {CircleX} from 'lucide-vue-next';

const breadcrumbs = [
   { title: 'Asignar horarios', href: '#' }
];

const props = defineProps<{
  doctor: { id: number; name: string; last_name: string } | null;
  therapy: { id: number; name: string } | null;
  doctors: Array<{ id: number; name: string; last_name: string }>;
  therapies: Array<{ id: number; name: string }>;
  weekdays: string[];
}>();

const form = ref<{
  id_doctor: number | null;
  id_therapy: number | null;
  selected_days: string[];
  start_time: string;
  end_time: string;
  interval_minutes: number | null;
}>({
  id_doctor: props.doctor?.id ?? null,
  id_therapy: props.therapy?.id ?? null,
  selected_days: [],
  start_time: '',
  end_time: '',
  interval_minutes: null,
});

const selectedDoctor = computed(() =>
  props.doctors.find((doc) => doc.id === form.value.id_doctor)
);
const selectedTherapy = computed(() =>
  props.therapies.find((ther) => ther.id === form.value.id_therapy)
);


const dayOptions = props.weekdays;


function toggleDay(day: string) {
  const index = form.value.selected_days.indexOf(day)
  if (index > -1) {
    form.value.selected_days.splice(index, 1)
  } else {
    form.value.selected_days.push(day)
  }
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
            if (props.doctor && props.therapy) {
                router.visit(route('doctor_therapies.index'));
            } else {
                router.visit(route('appointments.index'));
            }
        }
    });
};

const submit = () => {
  if (!form.value.id_doctor || !form.value.id_therapy) {
    Swal.fire({
      icon: 'warning',
      title: 'Datos incompletos',
      text: 'Debe seleccionar un terapeuta y una terapia.',
      confirmButtonColor: '#f59e0b',
    });
    return;
  }
  
  router.post(route('appointments.store'), form.value, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: '¡Carga horaria asignada!',
        text: 'La carga horaria se ha asignado exitosamente.',
        confirmButtonColor: '#3085d6',
      }).then(() => {
        if (props.doctor && props.therapy) {
          router.visit(route('doctor_therapies.index'));
        } else {
          router.visit(route('appointments.index'));
        }
      });
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'Ocurrió un error',
        text: 'Por favor verifica los campos del formulario.',
        confirmButtonColor: '#d33',
      });
      console.error(errors);
    },
  });
};
</script>

<template>
  <Head title="Asignar Horario" />
  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="p-6">

        <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                    <CircleX />Cancelar
        </Button>

        <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">


            <div class="grid grid-cols-2 gap-4">
              <!-- Doctor -->
              <div>
                <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Terapeuta</Label>
                <select v-model="form.id_doctor" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                  <option value="" disabled>Seleccione un terapeuta</option>
                  <option v-for="doc in props.doctors" :key="doc.id" :value="doc.id">
                    {{ doc.name }} {{ doc.last_name }}
                  </option>
                </select>
              </div>

              <!-- Terapia -->
              <div>
                <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Terapia</Label>
                <select v-model="form.id_therapy" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                  <option value="" disabled>Seleccione una terapia</option>
                  <option v-for="ther in props.therapies" :key="ther.id" :value="ther.id">
                    {{ ther.name }}
                  </option>
                </select>
              </div>

            </div>
            
            <!-- Días seleccionables -->
            <div class="space-y-2">
                <Label for="selected_days" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Días disponibles (puede seleccionar varios)
                </Label>

                <!-- Select oculto -->
                <select
                    id="selected_days"
                    v-model="form.selected_days"
                    multiple
                    class="hidden"
                >
                    <option
                    v-for="day in dayOptions"
                    :key="day"
                    :value="day"
                    >
                    {{ day }}
                    </option>
                </select>

                <!-- Visual personalizado -->
                <div class="w-180 flex flex-wrap gap-2 p-2 border-gray-300 dark:border-gray-600 rounded-md">
                    <template v-for="day in dayOptions" :key="day">
                    <button
                        type="button"
                        @click="toggleDay(day)"
                        :class="[
                        'inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium transition-all',
                        form.selected_days.includes(day)
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-200 text-gray-800 dark:bg-indigo-400 dark:text-gray-700'
                        ]"
                    >
                        {{ day }}
                    </button>
                    </template>
                </div>
            </div>


            <!-- Horario general -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora inicio</Label>
                    <input type="time" v-model="form.start_time" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                </div>

                <div>
                    <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora fin</Label>
                    <input type="time" v-model="form.end_time" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                </div>
            </div>

            <!-- Intervalo -->
            <div>
                <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Intervalo (minutos)</Label>
                <input type="number" v-model="form.interval_minutes" min="0" step="5" class=" h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
            </div>

            <Button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white"
                >
                Generar horarios
            </Button>

        </form>
    </div>
  </AppLayout>
</template>