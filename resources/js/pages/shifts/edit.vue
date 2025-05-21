<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CircleX } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { Appointment, Shifts, Therapy, User } from '@/types';

const breadcrumbs = [
  { title: 'Agendar Citas', href: '/shifts' },
  { title: 'Editar Cita', href: '#' }
];

const props = defineProps<{
  patients: User[];
  doctors: User[];
  therapies: Therapy[];
  appointments: Appointment[];
  shift: Shifts
}>();

const form = useForm({
  id_patient: props.shift.id_patient,
  id_therapy: props.shift.id_therapy,
  id_doctor: props.shift.id_doctor,
  id_appointment: props.shift.id_appointment,
  date: props.shift.date,
  is_recurring: props.shift.is_recurring ? 1 : 0,
});

const selectedDay = ref('');
const filteredDoctors = ref<User[]>([]);
const filteredAppointments = ref<Appointment[]>([]);

const allowedDates = computed(() => {
  if (!selectedDay.value) return [];
  const today = new Date();
  const result: string[] = [];

  const selected = selectedDay.value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

  for (let i = 0; i < 30; i++) {
    const date = new Date(today.getFullYear(), today.getMonth(), today.getDate() + i);
    const dayName = date.toLocaleDateString('es-ES', {
      weekday: 'long',
      timeZone: 'UTC',
    }).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    if (dayName === selected) {
      result.push(date.toISOString().split('T')[0]);
    }
  }
  return result;
});

function validateDate(event: Event) {
  const input = event.target as HTMLInputElement;
  const selected = input.value;
  if (!allowedDates.value.includes(selected)) {
    Swal.fire({
      icon: 'error',
      title: 'Fecha inválida',
      text: 'La fecha seleccionada no corresponde al día elegido.',
      confirmButtonColor: '#d33',
      confirmButtonText: 'Entendido'
    });
    form.date = '';
  }
}

function filterDoctors() {
  filteredDoctors.value = props.doctors.filter(doc =>
    props.appointments.some(app =>
      app.id_doctor === doc.id && app.id_therapy === form.id_therapy
    )
  );
}

function filterAppointments() {
  filteredAppointments.value = props.appointments.filter(app =>
    app.id_doctor === form.id_doctor &&
    app.id_therapy === form.id_therapy &&
    (Number(app.available) === 1 || app.id === form.id_appointment) // <- incluir la actual
  );
}

const formatAppointmentOption = (app: any) => {
  return `${capitalize(app.day)} de ${app.start_time?.slice(0, 5)} a ${app.end_time?.slice(0, 5)}`;
};

const capitalize = (str: string) =>
  str.charAt(0).toUpperCase() + str.slice(1);

watch(() => form.id_therapy, () => {
  form.id_doctor = null;
  form.id_appointment = null;
  selectedDay.value = '';
  filterDoctors();
  filteredAppointments.value = [];
});

watch(() => form.id_doctor, () => {
  form.id_appointment = null;
  selectedDay.value = '';
  filterAppointments();

  const uniqueDays = [...new Set(filteredAppointments.value.map(app => app.day))];
  selectedDay.value = uniqueDays[0] ?? '';
});

onMounted(() => {
  filterDoctors();
  filterAppointments();

  const selectedApp = props.appointments.find(app => app.id === form.id_appointment);
  selectedDay.value = selectedApp?.day || '';

});

const cancel = () => {
  Swal.fire({
    title: '¿Cancelar?',
    text: '¿Deseas salir sin guardar cambios?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí',
    cancelButtonText: 'No',
  }).then(result => {
    if (result.isConfirmed) router.visit(route('shifts.index'));
  });
};

const submit = () => {
  form.put(route('shifts.update', props.shift.id), {
    onSuccess: () => {
      Swal.fire('Cita actualizada', 'La cita fue actualizada exitosamente', 'success');
    },
    onError: () => {
      Swal.fire('Error', 'Hubo un problema al actualizar la cita.', 'error');
    },
  });
};
</script>

<template>
  <Head title="Editar Cita" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
      <CircleX /> Cancelar
    </Button>

    <div class="p-6">

      <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">

        <div class="grid grid-cols-2 gap-4">

          <!-- Paciente -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Paciente</Label>
            <select v-model="form.id_patient" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
              <option value="" disabled>Seleccione un Paciente</option>
              <option v-for="pat in props.patients" :key="pat.id" :value="pat.id">
                {{ pat.name }} {{ pat.last_name }}
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

          <!-- Doctor -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Terapeuta</Label>
            <select v-model="form.id_doctor" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" :disabled="!filteredDoctors.length" required>
              <option value="" disabled>Seleccione un Terapeuta</option>
              <option v-for="doc in filteredDoctors" :key="doc.id" :value="doc.id">
                {{ doc.name }} {{ doc.last_name }}
              </option>
            </select>
          </div>

          <!-- Día -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Día</Label>
            <select v-model="selectedDay" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
              <option disabled value="">Seleccione un día</option>
              <option v-for="day in [...new Set(filteredAppointments.map(app => app.day))]" :key="day" :value="day">
                {{ capitalize(day) }}
              </option>
            </select>
          </div>

          <!-- Horario -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Horario</Label>
            <select v-model="form.id_appointment" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" :disabled="!filteredAppointments.length" required>
              <option value="" disabled>Seleccione un horario</option>
              <option v-for="app in filteredAppointments.filter(app => app.day === selectedDay)" :key="app.id" :value="app.id">
                {{ formatAppointmentOption(app) }}
              </option>
            </select>
          </div>

          <!-- Fecha -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Fecha</Label>
            <Input
              type="date"
              v-model="form.date"
              :min="allowedDates[0]"
              :max="allowedDates[allowedDates.length - 1]"
              @change="validateDate"
              class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
              :disabled="!selectedDay"
              required
            />
          </div>

          <!-- Recurrencia -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">¿Cambio por emergencia?</Label>
            <select v-model="form.is_recurring" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
              <option :value="1">Sí</option>
              <option :value="0">No</option>
            </select>
          </div>

        </div>

        <div class="flex justify-end mt-6">
          <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white">Actualizar Cita</Button>
        </div>
        
      </form>
    </div>
  </AppLayout>
</template>
