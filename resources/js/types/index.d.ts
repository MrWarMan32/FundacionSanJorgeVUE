import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string | null;
    last_name: string | null;
    email: string | null;
    id_card: string | null;
    gender: string | null;
    birth_date: string  | null;
    age: number | null;
    ethnicity: string | null;
    disable_card: boolean | null;
    id_disable_card: string | null;
   
    disability_type: string | null;
    disability_level: string | null;
    disability_grade: number | null;
    cause_disability: string | null;
    diagnosis: string | null;
    
    representative_name: string | null;
    representative_last_name: string | null;
    representative_id_card: string | null;
    phone: string | null;

    status: string | null;
    user_type: string | null;

    id_address: string | null;

}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Address {
    id_province: number | null;
    id_canton: number | null;
    id_parish: number | null;
    site: string | null;
    principal_street: string | null;
    secondary_street: string | null;
    reference: string | null;
  }
