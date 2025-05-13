<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Shifts } from '@/types';
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {Pencil, Trash, CalendarHeart, CalendarSync} from 'lucide-vue-next';
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

const filteredShifts = computed(() =>
  selectedStatus.value === 'todos'
    ? props.shifts
    : props.shifts.filter((s) => s.status === selectedStatus.value)
);

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

                <Button as-child class="bg-indigo-500 text-white hover:bg-indigo-700">
                <Link href="/shifts/create">
                    <CalendarSync /> Autogenerar Citas
                </Link>
                </Button>
            </div>

            <div class="mb-4 flex items-center gap-4">
                <Label for="status">Filtrar por estado:</Label>
                <select
                    id="status"
                    v-model="selectedStatus"
                    class="border px-2 py-1 rounded"
                >
                    <option value="todos">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="completado">Completadas</option>
                </select>
            </div>

            <div class="rounded-xl border p-2">
                <Table>
                    <TableCaption>Lista de citas</TableCaption>
                    <TableHeader>
                        <TableRow>
                        <TableHead class="text-center">Paciente</TableHead>
                        <TableHead class="text-center">Terapia</TableHead>
                        <TableHead class="text-center">Doctor</TableHead>
                        <TableHead class="text-center">Fecha</TableHead>
                        <TableHead class="text-center">Hora</TableHead>
                        <TableHead class="text-center">Estado</TableHead>
                        <TableHead class="text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <template v-if="filteredShifts.length">
                        <TableRow
                            v-for="shift in filteredShifts"
                            :key="shift.id"
                            class="text-center"
                        >
                            <TableCell>{{ shift.patient?.name }} {{ shift.patient?.last_name }}</TableCell>
                            <TableCell>{{ shift.therapy?.name }}</TableCell>
                            <TableCell>{{ shift.doctor?.name }} {{ shift.doctor?.last_name }}</TableCell>
                            <TableCell>{{ shift.date }}</TableCell>
                            <TableCell>
                            {{ shift.appointment?.start_time }} - {{ shift.appointment?.end_time }}
                            </TableCell>
                            <TableCell>
                            <span
                                :class="{
                                'text-green-600': shift.status === 'completado',
                                'text-yellow-600': shift.status === 'pendiente',
                                }"
                            >
                                {{ shift.status }}
                            </span>
                            </TableCell>

                            <TableCell class="flex justify-center gap-2">

                                <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                    <Link :href="route('shifts.edit', shift.id)">
                                    <Pencil />
                                    </Link>
                                </Button>

                                <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="deleteShifts(shift.id)">
                                    <Trash />
                                </Button>

                            </TableCell>
                        </TableRow>
                        </template>

                        <template v-else>
                            <TableEmpty :colspan="7" >No hay citas registradas.</TableEmpty>
                        </template>
                    </TableBody>
                </Table>
            </div>
        </div>

    </AppLayout>
</template>