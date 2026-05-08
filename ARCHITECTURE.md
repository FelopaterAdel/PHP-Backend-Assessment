# Architectural Decisions

## Database Design
- **Soft Deletes**: Products use soft deletion to preserve data integrity rather than permanent removal
- **Type Casting**: Price field cast to `float` for accurate financial calculations
- **Constraints**: SKU enforced as unique at database level; timestamp tracking on all records
- **Factories**: `ProductFactory` and `UserFactory` for consistent test data generation

## API Structure
- **RESTful Pattern**: Standard CRUD operations with `/api/products` namespace
- **Specialized Endpoints**: 
  - `GET /api/products/low-stock` - Business logic for inventory monitoring
  - `POST /api/products/{id}/stock` - Dedicated stock adjustment endpoint
- **JSON-Only**: All responses return `JsonResponse` with appropriate HTTP status codes (201, 404, 422, etc.)

## Caching Strategy
- **Cache Versioning**: Increment global `products:cache_version` to invalidate entire product listing cache at once (avoids key enumeration issues)
- **TTL**: 1-hour cache duration for list results; per-page caching for pagination
- **Smart Invalidation**: Cache cleared on any data mutation (store, update, destroy, adjustStock)
- **Driver**: Database-backed cache for persistence and shared access across processes

## Validation & Error Handling
- **FormRequest Classes**: Dedicated request classes (`StoreProductRequest`, `UpdateProductRequest`, `AdjustStockRequest`) separate validation from business logic
- **Granular Rules**: 
  - `sometimes|required` for partial updates vs `required` for creation
  - `unique:products,sku,{id}` excludes current product from uniqueness check during updates
  - Custom messages for user-friendly error feedback
- **JSON Errors**: Custom Exception Handler returns `{ "message": "Validation failed", "errors": {...} }` with 422 status

## Testing & Data Integrity
- **Feature Tests**: HTTP integration tests with `RefreshDatabase` trait for test isolation
- **Factory-Based**: `ProductFactory` generates realistic Faker data
- **Assertions**: Verify soft deletes, database state, and JSON response structures
- **Error Cases**: Comprehensive validation testing with edge cases (zero quantities, duplicate SKUs, etc.)

## Key Design Rationale
| Decision | Benefit |
|----------|---------|
| Soft Deletes | Audit trail preservation; recover accidentally deleted products |
| Cache Versioning | O(1) cache invalidation vs O(n) key enumeration |
| FormRequest Classes | Reusable validation logic; single responsibility principle |
| JSON-Only API | Consistent contract; no content negotiation complexity |
| Database Caching | Shared access across multiple app instances; stateless scalability |

