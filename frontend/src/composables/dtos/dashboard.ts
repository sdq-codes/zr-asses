import type { DestinationDTO } from '@/composables/dtos/destination.ts'
import type { TaskDTO } from '@/composables/dtos/Task.ts'
import type { OriginDTO } from '@/composables/dtos/origin.ts'

export interface DashboardResponseData {
  origins: OriginDTO[];
  destinations: DestinationDTO[];
  tasks: TaskDTO[];
}

export interface DashboardResponseDTO {
  data: DashboardResponseData
}
