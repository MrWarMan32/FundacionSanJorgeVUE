<script setup lang="ts">
import { ref, computed } from 'vue'
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import {Pencil, Trash2, UserRoundPlus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { Appointment, Therapy, User } from '@/types';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';

const breadcrumbs = [
   { title: 'Carga Horaria', href: '#' }
];

const props = defineProps<{
  appointments: Appointment[];
  doctor: User[];
  therapies: Therapy[];
  patient : User[];
}>();

const form = useForm({});
const selectedDoctor = ref('')
const selectedTherapy = ref('')
const selectedDay = ref('')

// Filtrar en base a doctor y terapia
const filteredAppointments = computed(() => {
  return props.appointments?.filter((app) => {
    return (
      (!selectedDoctor.value || app.id_doctor === Number(selectedDoctor.value)) &&
      (!selectedTherapy.value || app.id_therapy === Number(selectedTherapy.value)) &&
      (!selectedDay.value || app.day === selectedDay.value)
    )
  })
})

const deleteAppointment = async (id: number) => {
  const confirm = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });

  if (confirm.isConfirmed) {
    form.delete(route('appointments.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire('¡Eliminado!', 'La cita fue eliminada correctamente.', 'success');
      },
      onError: () => {
        Swal.fire('Error', 'Ocurrió un error al eliminar la cita.', 'error');
      },
    });
  }
};

const formatTime = (time:string) => time.slice(0, 5)

</script>

<template>
    <Head title="Carga Horaria" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            
          <div class="flex justify-between gap-4 mb-6">

            <Button as-child class="bg-indigo-500 text-white hover:bg-indigo-700">
                <Link :href="route('appointments.create')">
                    <UserRoundPlus /> Agregar carga horaria
                </Link>
            </Button>

            <div class="flex gap-4">

              <select v-model="selectedDoctor" class="border px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full">
                  <option value="">Todos los Doctores</option>
                  <option v-for="doctor in doctor" :key="doctor.id" :value="doctor.id">
                      {{ doctor.name }} {{ doctor.last_name }}
                  </option>
              </select>

              <select v-model="selectedTherapy" class="border px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full">
                  <option value="">Todas las Terapias</option>
                  <option v-for="therapy in therapies" :key="therapy.id" :value="therapy.id">
                      {{ therapy.name }}
                  </option>
              </select>

              <select v-model="selectedDay" class="border px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full">
                <option value="">Todos los Días</option>
                <option value="Lunes">Lunes</option>
                <option value="Martes">Martes</option>
                <option value="Miercoles">Miércoles</option>
                <option value="Jueves">Jueves</option>
                <option value="Viernes">Viernes</option>
              </select>
            </div>

          </div>

          <div class="rounded-xl border p-2">
            <Table>
              <TableCaption>Lista de horarios asignados</TableCaption>
              <TableHeader>
                  <TableRow>
                  <TableHead class="text-center">Doctor</TableHead>
                  <TableHead class="text-center">Terapia</TableHead>
                  <TableHead class="text-center">Horario</TableHead>
                  <TableHead class="text-center">Estado</TableHead>
                  <TableHead class="text-center">Paciente</TableHead>
                  <TableHead class="text-center">Acciones</TableHead>
                  </TableRow>
              </TableHeader>

              <TableBody>
                  <template v-if="props.appointments.length">
                  <TableRow v-for="a in filteredAppointments" :key="a.id" class="text-center">
                      <TableCell>{{ a.doctor?.name }} {{ a.doctor?.last_name }}</TableCell>
                      <TableCell>{{ a.therapy?.name }}</TableCell>
                      <TableCell>{{ a.day }}, {{ a.start_time }} | {{ a.end_time }}</TableCell>
                      <TableCell>
                        <span :class="a.available ? 'text-green-400' : 'text-red-400'">
                            {{ a.available ? 'Disponible' : 'Ocupado' }}
                          </span>             
                      </TableCell>
                      <TableCell>
                        <span v-if="a.patient">{{ a.patient.name }} {{ a.patient.last_name }}</span>
                        <span v-else class="text-gray-400 italic">Sin asignar</span>
                    </TableCell> 

                      <TableCell class="flex justify-center gap-2">
                          <Link :href="route('appointments.edit', a.id)">
                              <Button class="bg-indigo-500 p-2 rounded hover:bg-indigo-700">
                              <Pencil class="w-4 h-4 text-black" />
                              </Button>
                          </Link>

                          <Button class="bg-red-500 p-2 rounded hover:bg-red-600" @click="deleteAppointment(a.id)">
                              <Trash2 class="w-4 h-4 text-black" />
                          </Button>
                      </TableCell>
                  </TableRow>
                  </template>
                  <template v-else>
                  <TableEmpty :colspan="6">No hay horarios registrados.</TableEmpty>
                  </template>
              </TableBody>
            </Table>
          </div>
        </div>
    </AppLayout>
</template>