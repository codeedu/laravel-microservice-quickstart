export interface ListResponse<T>{
    data: T[],
    links: {
        first: string,
        last: string,
        prev: string | null,
        next: string | null
    },
    meta: {
        current_page: number,
        from: number,
        last_page: number,
        path: string,
        per_page: number,
        to: number,
        total: number
    }
}

interface Timestampable{
    readonly created_at: string,
    readonly updated_at: string,
    readonly deleted_at: string | null
}

export interface Category extends Timestampable{
    readonly id: string,
    name: string,
    description: string,
    is_active: boolean
}

export interface CastMember extends Timestampable{
    readonly id: string,
    name: string,
    type: number
}

export interface Genre extends Timestampable{
    readonly id: string,
    name: string,
    is_active: boolean
    categories: Category[]
}