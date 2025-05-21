<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { SharedData, User, type DoctorTherapy } from '@/types';
import { Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {Pencil, Trash2, UserRoundPlus, ArrowLeftRight, CalendarArrowUp} from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { Link } from '@inertiajs/vue3';

interface PageProps extends SharedData{
  doctor_therapies: DoctorTherapy[];
  unassignedDoctors: User[];
}

const { props } = usePage<PageProps>();


const breadcrumbs = [
   { title: 'Terapeutas', href: '#' }
];



const deleteDoctorTherapy = (doctorId: number) => {
    Swal.fire({
    title: '¿Eliminar doctor?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('users.destroy', doctorId), {
        onSuccess: () => {
          Swal.fire({
            title: 'Eliminado',
            text: 'El doctor ha sido eliminado exitosamente.',
            icon: 'success',
            confirmButtonText: 'OK',
          }).then(() => {
            router.visit(route('doctor_therapies.index'));
          });
        },
        onError: () => {
          Swal.fire('Error', 'No se pudo eliminar el doctor.', 'error');
        },
      });
    }
  });
};
</script>

<template>
  <Head title="Asignaciones de Terapeutas" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
        
      <div class="flex justify-between mb-4">

        <Button as-child class="bg-indigo-500 text-white hover:bg-indigo-700">
          <Link href="/doctors/create">
              <UserRoundPlus /> Agregar Terapeuta
          </Link>
        </Button>

      </div>

      <div class="rounded-xl border p-2">
            <Table>
            <TableCaption>Lista de terapeutas asignados</TableCaption>
            <TableHeader>
                <TableRow>
                <TableHead class="text-center">Nombres</TableHead>
                <TableHead class="text-center">Cedula</TableHead>
                <TableHead class="text-center">Contacto</TableHead>
                <TableHead class="text-center">Lugar de Formacaion</TableHead>
                <TableHead class="text-center">Tutulo Obtenido</TableHead>
                <TableHead class="text-center">Terapia Asignada</TableHead>
                <TableHead class="text-center">Acciones</TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <template v-if="props.doctor_therapies && props.doctor_therapies.length">
                  <TableRow v-for="dt in props.doctor_therapies" :key="dt.id" class="text-center">
                      <TableCell>{{ dt.doctor?.name }} {{ dt.doctor?.last_name }}</TableCell>
                      <TableCell>{{ dt.doctor?.id_card }} </TableCell>
                      <TableCell>{{ dt.doctor?.phone}} </TableCell>
                      <TableCell>{{ dt.doctor?.university_name }} </TableCell>
                      <TableCell>{{ dt.doctor?.degree_title }} </TableCell>
                      <TableCell>{{ dt.therapy?.name }}</TableCell>
                      
                      <TableCell class="flex justify-center gap-2">

                      <Button as-child size="sm" class="bg-indigo-500 text-black hover:bg-indigo-700" title="Editar terapeuta">
                          <Link :href="route('doctors.edit', dt.id_doctor)">
                          <Pencil />
                          </Link>
                      </Button>

                      <Button as-child size="sm" class="bg-green-500 text-black hover:bg-green-700" title="Cambiar terapia">
                          <Link :href="route('doctor_therapies.edit', dt.id)">
                          <ArrowLeftRight />
                          </Link>
                      </Button>

                      <Button as-child size="sm" class="bg-yellow-500 text-black hover:bg-yellow-700" title="Agregar carga horaria"
                      >
                        <Link :href="route('appointments.create', { id_doctor: dt.id_doctor, id_therapy: dt.therapy?.id } )">
                          <CalendarArrowUp />
                        </Link>
                      </Button>

                      <Button size="sm" class="bg-red-500 text-black hover:bg-red-700" @click="deleteDoctorTherapy(dt.id_doctor)" title="Eliminar">
                          <Trash2 />
                      </Button>

                      </TableCell>
                  </TableRow>
                </template>
                <template v-else>
                  <TableEmpty :colspan="7">No hay terapeutas registrados.</TableEmpty>
                </template>
            </TableBody>
            </Table>
      </div>
    </div>

    <!-- tabla de terapeutas  -->
    <div class="p-4">

      <h2 class="text-xl font-semibold mt-10 mb-4 text-gray-800 dark:text-white">
        Doctores sin terapia asignada
      </h2>

      <div class="rounded-xl border p-2">
        <Table>
          <TableCaption>Lista de terapeutas sin terapia asignada</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead class="text-center">Nombres</TableHead>
              <TableHead class="text-center">Cédula</TableHead>
              <TableHead class="text-center">Contacto</TableHead>
              <TableHead class="text-center">Lugar de formación</TableHead>
              <TableHead class="text-center">Título obtenido</TableHead>
              <TableHead class="text-center">Acciones</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <template v-if="props.unassignedDoctors && props.unassignedDoctors.length">
              <TableRow
                v-for="doctor in props.unassignedDoctors"
                :key="doctor.id"
                class="text-center"
              >
                <TableCell>{{ doctor.name }} {{ doctor.last_name }}</TableCell>
                <TableCell>{{ doctor.id_card }}</TableCell>
                <TableCell>{{ doctor.phone }}</TableCell>
                <TableCell>{{ doctor.university_name }}</TableCell>
                <TableCell>{{ doctor.degree_title }}</TableCell>

                <TableCell class="flex justify-center gap-2">

                  <Button
                    as-child
                    size="sm"
                    class="bg-green-500 text-black hover:bg-green-700"
                  >
                    <Link
                      :href="route('doctor_therapies.create', { selectedDoctorId: doctor.id })"
                    >
                      Asignar terapia
                    </Link>
                  </Button>

                  <Button
                    as-child
                    size="sm"
                    class="bg-red-500 text-black hover:bg-red-700"
                    @click="deleteDoctorTherapy(doctor.id)"
                  >
                    <Trash2 />
                  </Button>
                </TableCell>
              </TableRow>
            </template>

            <template v-else>
              <TableEmpty :colspan="6" >No hay terapeutas sin asignación.</TableEmpty>
            </template>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
