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

export type BreadcrumbItemType = BreadcrumbItem;

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

    university_name: string | null;
    degree_title: number | null;
    graduation_year: string | null;
    speciality: string | null;
    certifications: string | null;

    status: string | null;
    user_type: string | null;

    id_address: string | null;
    // address?: {
    //     id: number;
    //     site: string;
    //     reference: string;
    //   };

}

export interface Therapy {
    id: number;
    name: string | null;
    description: string | null;
    duration: string | null;
}


export interface Address {
    id: number;
    id_user: number | null;
    id_province: number | null;
    id_canton: number | null;
    id_parish: number | null;
    site: string ;
    principal_street: string;
    secondary_street: string;
    reference: string;
    user?: {
        id: number;
        name: string;
        last_name: string;
      };
      province?: {
        id: number;
        name_province: string;
      };
      canton?: {
        id: number;
        name_canton: string;
      };
      parish?: {
        id: number;
        parroquia: string;
      };
  }
