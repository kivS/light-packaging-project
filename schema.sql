CREATE TABLE IF NOT EXISTS "user" ("id" integer, "name" text, "slug" text, "uid" text, "email" text, "login_hash" text, "created_at" datetime, "updated_at" datetime, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS "project" ("id" integer, "name" text, "description" text, "slug" text, "user_uid" text REFERENCES "user"(uid) ON UPDATE CASCADE ON DELETE CASCADE, "created_at" datetime, "uid" text, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS "document" ("id" integer, "project_uid" text references project(uid) ON UPDATE CASCADE ON DELETE CASCADE, "name" text, "uid" text, "created_at" datetime, "slug" text, "file_path" text, "file_uploaded_at" datetime, "file_original_name" text, PRIMARY KEY (id));
CREATE INDEX "index_project_user_uid" ON "project" ("user_uid");
CREATE INDEX "index_document_project_uid" ON "document" ("project_uid");
CREATE UNIQUE INDEX "index_user_uid" ON "user" ("uid");
CREATE UNIQUE INDEX "index_project_uid" ON "project" ("uid");