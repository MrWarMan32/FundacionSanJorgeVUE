<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup, SidebarGroupLabel, SidebarGroupAction, SidebarGroupContent } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {Folder, LayoutGrid,  Users, User, BookHeart, BriefcaseMedical, MapPin, CalendarDays, Calendar1, Plus, ChevronDown} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import Collapsible from './ui/collapsible/Collapsible.vue';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';

const platform = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

const users = [
    {
        title: 'Aspirantes',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Pacientes',
        href: '/patients',
        icon: User,
    },
    {
        title: 'Direcciones',
        href: '/addresses',
        icon: MapPin,
    },
];

const personal = [
    {
        title: 'Terapeutas',
        href: '/doctor_therapies',
        icon: BriefcaseMedical,
    },
    {
        title: 'Terapias',
        href: '/therapies',
        icon: BookHeart,
    },
    {
        title: 'Carga Horaria',
        href: '/appointments',
        icon: Calendar1,
    },
];

const shifts = [
    {
        title: 'Citas Semanales',
        href: '/shifts',
        icon: CalendarDays,
    },
    {
        title: 'Historial de Citas',
        href: '/shifts-register',
        icon: CalendarDays,
    },
];



const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/MrWarMan32/FundacionSanJorgeVUE/commits/main/?author=MrWarMan32',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>


        <SidebarContent>

            <SidebarGroup>
                <SidebarGroupLabel>Plataforma</SidebarGroupLabel>  
                <SidebarGroupContent>
                    <SidebarMenu>

                        <SidebarMenuItem v-for="plat in platform" :key="plat.title">
                            <SidebarMenuButton asChild>
                                <a :href="plat.href">
                                <component :is="plat.icon" />
                                <span>{{plat.title}}</span>
                                </a>
                            </SidebarMenuButton>
                        </SidebarMenuItem>

                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
            
            <Collapsible defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel asChild>
                        <CollapsibleTrigger>
                            Gestion de Usuarios
                            <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>  

                    <CollapsibleContent>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem v-for="user in users" :key="user.title">
                                    <SidebarMenuButton asChild>
                                        <a :href="user.href">
                                        <component :is="user.icon" />
                                        <span>{{user.title}}</span>
                                        </a>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                    </SidebarGroupContent>
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>

            <Collapsible defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel asChild>
                        <CollapsibleTrigger>
                            Gestion de Personal
                            <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>  

                    <CollapsibleContent>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem v-for="per in personal" :key="per.title">
                                    <SidebarMenuButton asChild>
                                        <a :href="per.href">
                                        <component :is="per.icon" />
                                        <span>{{per.title}}</span>
                                        </a>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                    </SidebarGroupContent>
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>

            <Collapsible defaultOpen class="group/collapsible">
                <SidebarGroup>
                    <SidebarGroupLabel asChild>
                        <CollapsibleTrigger>
                            Gestion de Citas
                            <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                        </CollapsibleTrigger>
                    </SidebarGroupLabel>  

                    <CollapsibleContent>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem v-for="shift in shifts" :key="shift.title">
                                    <SidebarMenuButton asChild>
                                        <a :href="shift.href">
                                        <component :is="shift.icon" />
                                        <span>{{shift.title}}</span>
                                        </a>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                    </SidebarGroupContent>
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>

        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
