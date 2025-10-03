export interface OriginDTO {
  id: string;
  name: string;
  description: string;
  image: string;
  created_at: string;
  updated_at: string;
}

export interface UpdateOriginDTO {
  name?: string;
  description?: string;
  image?: string;
}

