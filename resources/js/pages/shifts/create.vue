<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CircleX } from 'lucide-vue-next';
import Swal from 'sweetalert2'
import Datepicker from 'vue3-datepicker';

const breadcrumbs = [
  { title: 'Agendar Citas', href: '/shifts' },
  { title: 'Nueva Cita', href: '#' }
];

const props = defineProps<{
  patients: Array<{ id: number; name: string; last_name: string }>;
  doctors: Array<{ id: number; name: string; last_name: string }>;
  therapies: Array<{ id: number; name: string }>;
  appointments: Array<{ id: number; day: string; start_time: string; end_time: string; id_doctor: number; id_therapy: number; available: number }>;
}>();

const form = useForm({
  id_patient: '',
  id_therapy: '',
  id_doctor: '',
  id_appointment: '',
  date: '',
});

const selectedDay = ref('');
const filteredDoctors = ref<{ id: number; name: string; last_name: string }[]>([]);
const filteredAppointments = ref<{ id: number; day: string; start_time: string; end_time: string; id_doctor: number; id_therapy: number; available?: number }[]>([]);


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
    })
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');

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

watch(() => form.id_therapy, (therapyId) => {
  if (!therapyId) {
    filteredDoctors.value = [];
    form.id_doctor = '';
    filteredAppointments.value = [];
    return;
  }

  const parsedTherapyId = Number(therapyId);

  filteredDoctors.value = props.doctors.filter((doc) => {
    return props.appointments.some(app =>
      app.id_doctor === doc.id && app.id_therapy === parsedTherapyId
    );
  });

  form.id_doctor = '';
  form.id_appointment = '';
  filteredAppointments.value = [];
});

watch(() => form.id_doctor, (doctorId) => {
  if (!doctorId || !form.id_therapy) {
    filteredAppointments.value = [];
    form.id_appointment = '';
    selectedDay.value = '';
    return;
  }

  const parsedDoctorId = Number(doctorId);
  const parsedTherapyId = Number(form.id_therapy);

  filteredAppointments.value = props.appointments.filter(app =>
    app.id_doctor === parsedDoctorId && app.id_therapy === parsedTherapyId && app.available === 1
  );

  // Extraer los días disponibles únicos
  const uniqueDays = [...new Set(filteredAppointments.value.map(app => app.day))];
  selectedDay.value = uniqueDays[0] ?? '';

  form.id_appointment = '';
});

const formatAppointmentOption = (app: {
  day: string;
  start_time: string;
  end_time: string;
}) => {
  const dayFormatted = capitalize(app.day);
  const start = app.start_time?.slice(0, 5);
  const end = app.end_time?.slice(0, 5);
  return `${dayFormatted} de ${start} a ${end}`;
};


const capitalize = (str: string) =>
  str.charAt(0).toUpperCase() + str.slice(1);

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
      router.visit(route('shifts.index'));
    }
  });
};

const submit = () => {
  form.post(route('shifts.store'), {
    onSuccess: () => {
      Swal.fire('Cita creada', 'La cita fue registrada exitosamente', 'success');
    },
    onError: () => {
      Swal.fire('Error', 'Hubo un problema al guardar la cita.', 'error');
    },
  });
};

</script>

<template>
  <Head title="Nueva cita" />

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

          <!-- Dia -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Día</Label>
            <select v-model="selectedDay" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                <option disabled value="">Seleccione un día</option>
                <option v-for="day in [...new Set(filteredAppointments.map(app => app.day))]" :key="day" :value="day">
                {{ capitalize(day) }}
                </option>
            </select>
            </div>

          <!-- Horarios -->
          <div>
            <Label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-6">Horarios</Label>
                <select v-model="form.id_appointment" class="h-8 mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" :disabled="!filteredAppointments.length" required>
                    <option value="" disabled>Seleccione un Horario</option>
                    <option
                        v-for="app in filteredAppointments.filter(app => app.day === selectedDay)"
                        :key="app.id"
                        :value="app.id"
                        >
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

        </div>

        <div class="flex justify-end mt-6">
          <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white">Guardar Cita</Button>
        </div>
        
      </form>
    </div>
  </AppLayout>
</template>
