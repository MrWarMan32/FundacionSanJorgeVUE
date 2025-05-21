<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head} from '@inertiajs/vue3';
import { Shifts } from '@/types';
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import {FilePlus2} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3'

const breadcrumbs = [
   { title: 'Registro de Citas', href: '#' }
];

const props = defineProps<{
  shifts: Shifts[];
}>();

// Filtrar citas completadas
const filteredShifts = computed(() => {  
    return props.shifts.filter(shift => {
        return shift.status === 'completada';
    });
});


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

</script>


<template>
    <Head title="Registro de Citas" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-4">
            
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
                            <TableCell>
                                <Button as-child size="sm" class="bg-orange-500 text-black hover:bg-orange-700" title="Generar certificado">
                                    <a :href="route('shifts.certificate', shift.id)" target="_blank">
                                    <FilePlus2 />
                                    </a>
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