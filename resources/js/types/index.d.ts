import { type Config } from 'ziggy-js'

export interface User {
    id: number
    name: string
    email: string
    email_verified_at: string
}

export interface Star {
    id?: number
    name: string
    first_name: string
    image: string
    description: string
    newImage?: File
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User
    }
    ziggy: Config & { location: string }
}
