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
    href?: string;
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
}

export interface Therapy {
    id: number;
    name: string | null;
    description: string | null;
    duration: string | null;
}

export interface DoctorTherapy {
    id: number;
    id_doctor: number;
    id_therapy: number;
    doctor?: {
        id: number;
        name: string;
        last_name: string;
        id_card: string;
        phone : string;
        university_name: string;
        degree_title: string;
        graduation_year: string;
        speciality: string;
        certifications: string;
      };
      therapy?: {
        id: number;
        name: string;
        description: string;
        duration: string;
      };
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

  export interface Appointment {
    id: number;
    id_doctor: number;
    id_patient: number;
    id_therapy: number;
    day: string;
    start_time: string;
    end_time: string;
    available: boolean | null;
    doctor: {
      id: number;
      name: string;
      last_name: string;
    };
    therapy: {
      id: number;
      name: string;
    };
    patient: {
      id: number;
      name: string;
      last_name: string;
    };
  }

  export interface Shifts {
    id: number;
    id_doctor: number  | null;
    id_patient: number | null;
    id_therapy: number | null;
    id_appointment: number | null;
    is_recurring: boolean | null;
    id_parent_shift: number | null;
    date: string;
    status: 'pendiente' | 'completada';
    notes?: string | null;
    doctor?: {
        id: number;
        name: string;
        last_name: string;
        id_card: string;
        phone : string;
        university_name: string;
        degree_title: string;
        graduation_year: string;
        speciality: string;
        certifications: string;
      };
      patient?: {
        id: number;
        name: string;
        last_name: string;
      };
      therapy?: {
        id: number;
        name: string;
        description: string;
        duration: string;
      };
      appointment?: {
        id: number;
        day: string;
        start_time: string;
        end_time: string;
      };
  }
