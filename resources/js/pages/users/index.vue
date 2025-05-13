<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage, Link, router} from '@inertiajs/vue3';
import {User, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';
import {Button} from '@/components/ui/button';

import {Pencil, Trash, UserRoundPlus, UserCheck, MapPinPlus} from 'lucide-vue-next';
import {computed} from 'vue';
import Swal from 'sweetalert2';



interface UsersPageProps extends SharedData{
    users : User[];
}

const {props} = usePage<UsersPageProps>();

// Filtrar solo aspirantes
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        return user.user_type === 'usuario' && user.status === 'aspirante';
    });
});

const convertToPaciente = async (id: number) => {
  Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Deseas convertir a este aspirante en paciente?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, convertir',
    cancelButtonText: 'No, cancelar',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await router.patch(
          route('users.convertToPaciente', id),
          {},
          {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
              router.visit(route('users.index'));
              Swal.fire(
                '¡Convertido!',
                'El aspirante ha sido convertido en paciente.',
                'success'
              );
              console.log('Aspirante convertido a paciente con éxito.');
            },
            onError: (errors) => {
              console.error('Error al convertir a paciente:', errors);
              let errorMessage = 'Hubo un error al intentar convertir a paciente.';
                if (errors && typeof errors === 'object') {
                    for (const key in errors) {
                        const errorValue = errors[key];
                            if (Array.isArray(errorValue)) {
                                errorMessage += `<br><strong>${key}:</strong> ${errorValue.join(', ')}`;
                            } else {
                                errorMessage += `<br><strong>${key}:</strong> ${errorValue}`;
                            }
                    }
                }
              Swal.fire({
                title: 'Error',
                html: errorMessage,
                icon: 'error',
                confirmButtonText: 'Ok'
              });
            },
          }
        );
      } catch (error: any) {
        console.error('Error inesperado:', error);
        Swal.fire( 
          '¡Error!',
          'Ocurrió un error inesperado.',
          'error'
        );
      }
    }
  });
};


const breadcrumbs: BreadcrumbItem[] = [
    
    {title: 'Aspirantes', href: '/users'}
];

const deleteUser = async (id: number) => {
    Swal.fire({
        title: '¿Eliminar Aspirante?',
        text: 'Esta acción no se puede deshacer!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await router.delete(route('users.destroy', id), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        router.visit(route('users.index'));
                        Swal.fire(
                            '¡Eliminado!',
                            'El aspirante ha sido eliminado con éxito.',
                            'success'
                        );
                        console.log('Aspirante eliminado con éxito.');
                    },
                    onError: (errors) => {
                        let errorMessage = 'Hubo un error al intentar eliminar al aspirante.';
                        if (errors && typeof errors === 'object') {
                            for (const key in errors) {
                                const errorValue = errors[key];
                                errorMessage += `<br><strong>${key}:</strong> ${Array.isArray(errorValue) ? errorValue.join(', ') : errorValue}`;
                            }
                        }
                        Swal.fire({
                            title: 'Error',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'Ok',
                        });
                    },
                });
            } catch (error: any) {
                Swal.fire(
                    '¡Error!',
                    'Ocurrió un error inesperado.',
                    'error'
                );
            }
        }
    });
};

</script>

<template>
    <Head title="Aspirantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        
            <div class="p-4">
                <div class="flex">
                    <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                        <Link href="/users/create">
                            <UserRoundPlus /> Agregar Aspirante
                        </Link>
                    </Button>
                </div>
            </div>
            <div class="p-4">
                <div class="rounded-xl border p-2">
                    <Table>
                    <TableCaption>Lista de Aspirantes</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[200px] text-center">Nombres</TableHead>
                                <TableHead class="w-[200px] text-center">Cedula</TableHead>
                                <TableHead class="w-[200px] text-center">Tipo Discapacidad</TableHead>
                                <TableHead class="w-[200px] text-center">Diagnostico</TableHead>
                                <TableHead class="w-[200px] text-center">Contacto</TableHead>
                                <TableHead class="w-[200px] text-center">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <template v-if="filteredUsers.length > 0">
                                <TableRow v-for="user in filteredUsers" :key="user.id" class="text-center">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.id_card }}</TableCell>
                                    <TableCell>
                                        <div class="flex flex-wrap justify-center gap-1">
                                        <template v-if="user.disability_type">
                                            <template v-for="(disability, index) in JSON.parse(user.disability_type)">
                                            <div v-if="index % 2 === 0" class="flex justify-center w-full">
                                                <span :key="index" class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">
                                                {{ disability }}
                                                </span>
                                                <span v-if="JSON.parse(user.disability_type)[index + 1]" :key="index + 1" class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">
                                                {{ JSON.parse(user.disability_type)[index + 1] }}
                                                </span>
                                            </div>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <span class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">No especificado</span>
                                        </template>
                                        </div>
                                    </TableCell>
                                    <TableCell>{{ user.diagnosis }}</TableCell>
                                    <TableCell>{{ user.phone }}</TableCell>

                                    <TableCell class="flex justify-center items-center h-full gap-2">

                                        <Button size="sm" class="bg-green-500 text-white hover:bg-green-700" @click="convertToPaciente(user.id)">
                                            <UserCheck />
                                        </Button>

                                        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                            <Link :href="route('users.edit', { user: user.id })">
                                                <Pencil />
                                            </Link>
                                        </Button>

                                        <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                            <Link :href="route('addresses.edit', { id_user: user.id })">
                                                <MapPinPlus />
                                            </Link>
                                        </Button>

                                        <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="deleteUser(user.id)">
                                            <Trash /> 
                                        </Button>

                                    </TableCell>
                                </TableRow>
                            </template>
                            <template v-else>
                                <TableEmpty :colspan="6"> No hay aspirantes registrados. </TableEmpty>
                            </template>
                        </TableBody>
                    </Table>
                </div>
        </div>
    </AppLayout>
</template>
