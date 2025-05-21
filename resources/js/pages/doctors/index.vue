<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage, Link, router} from '@inertiajs/vue3';
import {User, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';
import {Button} from '@/components/ui/button';

import {Pencil, Trash2, UserRoundPlus} from 'lucide-vue-next';
import {computed} from 'vue';
import Swal from 'sweetalert2';


const breadcrumbs: BreadcrumbItem[] = [
    {title: 'Terapeutas', href: '#'}
];

interface UsersPageProps extends SharedData{
    users : User[];
}

const {props} = usePage<UsersPageProps>();

// Filtrar solo doctores
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        return user.user_type === 'doctor';
    });
});


const deleteUser = async (id: number) => {
    Swal.fire({
        title: '¿Eliminar Terapeuta?',
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
                        router.visit(route('doctors.index'));
                        Swal.fire(
                            '¡Eliminado!',
                            'El terapeuta ha sido eliminado con éxito.',
                            'success'
                        );
                        console.log('doctor eliminado con éxito.');
                    },
                    onError: (errors) => {
                        let errorMessage = 'Hubo un error al intentar eliminar al terapeuta.';
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
    <Head title="Terapeutas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="p-4">
            
            <div class="flex margin-top-4 mb-4">
                <Button as-child class="bg-indigo-500 text-white hover:bg-indigo-700">
                    <Link href="/doctors/create">
                        <UserRoundPlus /> Agregar Terapeuta
                    </Link>
                </Button>
            </div>

            <div class="ralative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table>
                    <TableCaption>Lista de Terapeutas</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[200px] text-center">Nombres</TableHead>
                            <TableHead class="w-[200px] text-center">Cedula</TableHead>
                            <TableHead class="w-[200px] text-center">Lugar de Formacion</TableHead>
                            <TableHead class="w-[200px] text-center">Titulo obtenido</TableHead>
                            <TableHead class="w-[200px] text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <template v-if="filteredUsers.length > 0">
                            <TableRow v-for="user in filteredUsers" :key="user.id" class="text-center">
                                <TableCell>{{ user.name }}</TableCell>
                                <TableCell>{{ user.id_card }}</TableCell>
                                <TableCell>{{ user.university_name }}</TableCell>
                                <TableCell>{{ user.degree_title }}</TableCell>

                                <TableCell class="flex justify-center items-center h-full gap-2">

                                    <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                        <Link :href="route('doctors.edit', { id: user.id })">
                                            <Pencil />
                                        </Link>
                                    </Button>

                                    <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="deleteUser(user.id)">
                                        <Trash2 /> 
                                    </Button>

                                </TableCell>
                            </TableRow>
                        </template>
                        <template v-else>
                            <TableEmpty :colspan="5"> No hay terapeutas registrados. </TableEmpty>
                        </template>
                    </TableBody>
                </Table>
            </div>
    </div>
    </AppLayout>
</template>
