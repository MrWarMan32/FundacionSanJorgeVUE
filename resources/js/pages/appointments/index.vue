<script setup lang="ts">
import { ref, computed } from 'vue'
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import { Link } from '@inertiajs/vue3';
import {Pencil, Trash2, UserRoundPlus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { Appointment } from '@/types';

const breadcrumbs = [
   { title: 'Carga Horaria', href: '#' }
];

const props = defineProps<{
  appointments: Appointment[];
  doctor: { id: number; name: string; last_name: string }[];
  therapies: { id: number; name: string }[];
}>();

const selectedDoctor = ref('')
const selectedTherapy = ref('')

// Filtrar en base a doctor y terapia
const filteredAppointments = computed(() => {
  return props.appointments?.filter((app) => {
    return (
      (!selectedDoctor.value || app.id_doctor === Number(selectedDoctor.value)) &&
      (!selectedTherapy.value || app.id_therapy === Number(selectedTherapy.value))
    )
  })
})

const deleteAppointment = (id: number) => {
  if (confirm('¿Estás seguro de eliminar este horario?')) {
    router.delete(route('appointments.destroy', id));
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

                    <select v-model="selectedDoctor" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md">
                        <option value="">Todos los Doctores</option>
                        <option v-for="doctor in doctor" :key="doctor.id" :value="doctor.id">
                            {{ doctor.name }} {{ doctor.last_name }}
                        </option>
                    </select>

                    <select v-model="selectedTherapy" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md">
                        <option value="">Todas las Terapias</option>
                        <option v-for="therapy in therapies" :key="therapy.id" :value="therapy.id">
                            {{ therapy.name }}
                        </option>
                    </select>
                </div>

            </div>

                <Table>
                    <TableCaption>Lista de horarios asignados</TableCaption>
                    <TableHeader>
                        <TableRow>
                        <TableHead class="text-center">Doctor</TableHead>
                        <TableHead class="text-center">Terapia</TableHead>
                        <TableHead class="text-center">Día</TableHead>
                        <TableHead class="text-center">Hora de Inicio</TableHead>
                        <TableHead class="text-center">Hora de Fin</TableHead>
                        <TableHead class="text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <template v-if="props.appointments.length">
                        <TableRow v-for="a in filteredAppointments" :key="a.id" class="text-center">
                            <TableCell>{{ a.doctor?.name }} {{ a.doctor?.last_name }}</TableCell>
                            <TableCell>{{ a.therapy?.name }}</TableCell>
                            <TableCell>{{ a.day }}</TableCell>
                            <TableCell>{{ a.start_time }}</TableCell>
                            <TableCell>{{ a.end_time }}</TableCell>
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
    </AppLayout>


</template>