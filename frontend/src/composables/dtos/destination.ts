export interface DestinationDTO {
  id: string;
  name: string;
  description: string;
  image: string;
  created_at: string;
  updated_at: string;
}

export interface UpdateDestinationDTO {
  name?: string;
  description?: string;
  image?: string;
}

export interface CreateDestinationDTO {
  name: string;
  description: string;
  image: string;
}
