<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage, Link, router} from '@inertiajs/vue3';
import {User, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';
import {Button} from '@/components/ui/button';

import {Pencil, Trash, UserRoundPlus, Eye, UserCheck} from 'lucide-vue-next';
import {computed} from 'vue';


interface UsersPageProps extends SharedData{
    users : User[];
}

const {props} = usePage<UsersPageProps>();

// Filtrar solo pacientes
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        return user.user_type === 'usuario' && user.status === 'aspirante';
    });
});

const convertToPaciente = async (id: number) => {
    if (confirm('¿Estás seguro de que deseas convertir a este aspirante en paciente?')) {
        try {
            await router.patch(
                route('users.convertToPaciente', id),
                {}, // Datos (vacío, ya que solo estamos actualizando el estado)
                { // Opciones
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        router.visit(route('users.index')); // Recargar la lista para ver el cambio
                        console.log('Aspirante convertido a paciente con éxito.');
                    },
                    onError: (errors) => {
                        console.error('Error al convertir a paciente:', errors);
                        alert('Hubo un error al intentar convertir a paciente.');
                    },
                }
            );
        } catch (error: any) {
            console.error('Error inesperado:', error);
            alert('Ocurrió un error inesperado.');
        }
    }
};



const breadcrumbs: BreadcrumbItem[] = [
    
    {title: 'Aspirantes', href: '/users'}
];

const deleteUser = async (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este aspirante?')) {
        router.delete(route('users.destroy', id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                router.visit(route('users.index'));
                console.log('Aspirante eliminado con éxito.');
            },
            onError: () => {
                console.error('Error al eliminar el aspirante.');
            }
        });
    }
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

        <div class="ralative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
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
                            <TableCell>{{ user.disability_type }}</TableCell>
                            <TableCell>{{ user.diagnosis }}</TableCell>
                            <TableCell>{{ user.phone }}</TableCell>
                            <TableCell class="flex justify-center gap-2">

                                <Button size="sm" class="bg-green-500 text-white hover:bg-green-700" @click="convertToPaciente(user.id)">
                                    <UserCheck />
                                </Button>

                                <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                    <Link :href="`/users/${user.id}/edit`"><Pencil /></Link>
                                </Button>
                                
                                <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700">
                                    <Link :href="`/users/${user.id}/show`"><Eye /></Link>
                                </Button>

                                <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="deleteUser(user.id)">
                                    <Trash /> 
                                </Button>

                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableEmpty colspan = "6"> No hay aspirantes registrados. </TableEmpty>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
