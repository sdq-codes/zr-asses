import type { OriginDTO } from '@/composables/dtos/origin.ts'
import type { DestinationDTO } from '@/composables/dtos/destination.ts'

export interface TaskDTO {
  id: string;
  origin_id: string;
  origin: OriginDTO;
  destination: DestinationDTO;
  destination_id: string;
  schedule_expression: string;
  next_run_at: string;
  last_run_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface UpdateTaskDTO {
  origin_id?: string;
  destination_id?: string;
  schedule_expression?: string;
}

export interface CreateTaskDTO {
  origin_id: string;
  destination_id: string;
  schedule_expression: string;
}
