<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Shifts } from '@/types';
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {Pencil, Trash2, CalendarHeart, CalendarSync, FilePlus2} from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const breadcrumbs = [
   { title: 'Agendar Cita', href: '#' }
];

const props = defineProps<{
  shifts: Shifts[];
}>();


const selectedStatus = ref<'todos' | 'pendiente' | 'completado'>('todos');

// Filtrar citas completadas
const filteredShifts = computed(() => {  
    return props.shifts.filter(shift => {
        return shift.status === 'pendiente';
    });
});

const deleteShifts = (shiftId: number) => {
    Swal.fire({
    title: '¿Eliminar cita?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('shifts.destroy', shiftId), {
        onSuccess: () => {
          Swal.fire({
            title: 'Eliminado',
            text: 'La cita ha sido eliminado exitosamente.',
            icon: 'success',
            confirmButtonText: 'OK',
          }).then(() => {
            router.visit(route('shifts.index'));
          });
        },
        onError: () => {
          Swal.fire('Error', 'No se pudo eliminar la cita.', 'error');
        },
      });
    }
  });
};

const formatShiftDisplay = (
  dateStr: string,
  appointment?: { day?: string; start_time?: string; end_time?: string }
): string => {
  if (!dateStr || !appointment) return '';

  const [year, month, day] = dateStr.split('-').map(Number);
  const date = new Date(year, month - 1, day)

  const options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  };

  const formattedDate = date.toLocaleDateString('es-ES', options); // "lunes, 20 de mayo"

  const start = appointment.start_time?.slice(0, 5) || '';
  const end = appointment.end_time?.slice(0, 5) || '';

  return `${capitalize(formattedDate)} | ${start} - ${end}`;
};

// Capitaliza la primera letra
const capitalize = (text: string) => text.charAt(0).toUpperCase() + text.slice(1);


const generateWeekly = async () => {
  const confirm = await Swal.fire({
    title: '¿Generar citas?',
    text: '¿Deseas generar las citas para la siguiente semana?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, generar',
    cancelButtonText: 'Cancelar',
  });

  if (confirm.isConfirmed) {
    router.post(route('shifts.generateWeekly'), {}, {
      onSuccess: () => {
        Swal.fire('Éxito', 'Las citas fueron generadas correctamente.', 'success');
      },
      onError: () => {
        Swal.fire('Error', 'Hubo un error al generar las citas.', 'error');
      }
    });
  }
};



</script>

<template>
    <Head title="Citas" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-4">
            <div class="flex mb-4 gap-2">
                <Button as-child class="bg-indigo-500 text-white hover:bg-indigo-700">
                <Link href="/shifts/create">
                    <CalendarHeart /> Agendar cita
                </Link>
                </Button>

                <Button class="bg-indigo-500 text-white hover:bg-indigo-700"  @click="generateWeekly">
                  <CalendarSync /> Generar Citas
                </Button>
            </div>

            <div class="rounded-xl border p-2">
                <Table>
                    <TableCaption>Lista de citas</TableCaption>
                    <TableHeader>
                        <TableRow>
                        <TableHead class="text-center">Paciente</TableHead>
                        <TableHead class="text-center">Terapia</TableHead>
                        <TableHead class="text-center">Doctor</TableHead>
                        <TableHead class="text-center">Horario</TableHead>
                        <TableHead class="text-center">Estado</TableHead>
                        <TableHead class="text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow
                            v-for="shift in filteredShifts"
                            :key="shift.id"
                            class="text-center"
                        >
                            <TableCell>{{ shift.patient?.name }} {{ shift.patient?.last_name }}</TableCell>
                            <TableCell>{{ shift.therapy?.name }}</TableCell>
                            <TableCell>{{ shift.doctor?.name }} {{ shift.doctor?.last_name }}</TableCell>
                            <TableCell>{{ formatShiftDisplay(shift.date, shift.appointment) }}</TableCell>
                            <TableCell>
                            <span
                                :class="{
                                'text-green-600': shift.status === 'completada',
                                'text-yellow-600': shift.status === 'pendiente',
                                }"
                            >
                                {{ shift.status }}
                            </span>
                            </TableCell>

                            <TableCell class="flex justify-center gap-2">

                                <Button as-child size="sm" class="bg-indigo-500 text-black hover:bg-indigo-700">
                                    <Link :href="route('shifts.edit', shift.id)">
                                    <Pencil />
                                    </Link>
                                </Button>

                                <Button as-child size="sm" class="bg-green-500 text-black hover:bg-green-700">
                                    <a :href="route('shifts.certificate', shift.id)" target="_blank">
                                    <FilePlus2 />
                                    </a>
                                </Button>
                                
                                <Button size="sm" class="bg-red-500 text-black hover:bg-red-700" @click="deleteShifts(shift.id)">
                                    <Trash2 />
                                </Button>

                            </TableCell>
                        </TableRow>
                        
                        <template>
                            <TableEmpty :colspan="7" >No hay citas registradas.</TableEmpty>
                        </template>
                    </TableBody>
                </Table>
            </div>
        </div>

    </AppLayout>
</template>