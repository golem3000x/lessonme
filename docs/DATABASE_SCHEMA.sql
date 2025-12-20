--
-- PostgreSQL database dump
--

\restrict lzRRbi7kl34EPieJ2cKreuQEc1PLBMtPqx2jttfJE64feJx7qx0fHGQAEHYvcYA

-- Dumped from database version 17.7 (Debian 17.7-3.pgdg13+1)
-- Dumped by pg_dump version 17.7 (Debian 17.7-3.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: vector; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS vector WITH SCHEMA public;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: chat_sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.chat_sessions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    last_message_at timestamp(0) without time zone,
    unread_count integer DEFAULT 0 NOT NULL,
    status character varying(255) DEFAULT 'open'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT chat_sessions_status_check CHECK (((status)::text = ANY ((ARRAY['open'::character varying, 'closed'::character varying])::text[])))
);


--
-- Name: chat_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.chat_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: chat_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.chat_sessions_id_seq OWNED BY public.chat_sessions.id;


--
-- Name: course_assignments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.course_assignments (
    id bigint NOT NULL,
    course_id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    student_id bigint NOT NULL,
    assigned_at timestamp(0) without time zone NOT NULL,
    due_date timestamp(0) without time zone,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: course_assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.course_assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: course_assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.course_assignments_id_seq OWNED BY public.course_assignments.id;


--
-- Name: course_lessons; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.course_lessons (
    id bigint NOT NULL,
    course_id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    "order" integer DEFAULT 0 NOT NULL,
    is_required boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: course_lessons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.course_lessons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: course_lessons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.course_lessons_id_seq OWNED BY public.course_lessons.id;


--
-- Name: course_progress; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.course_progress (
    id bigint NOT NULL,
    course_id bigint NOT NULL,
    user_id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    score integer DEFAULT 0 NOT NULL,
    is_passed boolean DEFAULT false NOT NULL,
    completed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: course_progress_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.course_progress_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: course_progress_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.course_progress_id_seq OWNED BY public.course_progress.id;


--
-- Name: courses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.courses (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    user_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    language_level character varying(2),
    lesson_lang character varying(255) NOT NULL,
    cover_image character varying(255),
    is_published boolean DEFAULT false NOT NULL,
    passing_score integer DEFAULT 70 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: courses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.courses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: courses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.courses_id_seq OWNED BY public.courses.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: keyword_translations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.keyword_translations (
    id bigint NOT NULL,
    keyword_id bigint NOT NULL,
    language_code character varying(5) NOT NULL,
    value character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: keyword_translations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.keyword_translations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: keyword_translations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.keyword_translations_id_seq OWNED BY public.keyword_translations.id;


--
-- Name: keywords; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.keywords (
    id bigint NOT NULL,
    user_id bigint,
    topic_id bigint,
    slug character varying(255) NOT NULL,
    is_system boolean DEFAULT false NOT NULL,
    is_approved boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: keywords_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.keywords_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: keywords_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.keywords_id_seq OWNED BY public.keywords.id;


--
-- Name: lesson_assignments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_assignments (
    id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    student_id bigint NOT NULL,
    assigned_version json,
    assigned_at timestamp(0) without time zone NOT NULL,
    due_date timestamp(0) without time zone,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lesson_assignments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_assignments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_assignments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_assignments_id_seq OWNED BY public.lesson_assignments.id;


--
-- Name: lesson_attempts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_attempts (
    id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    user_id bigint NOT NULL,
    assignment_id bigint,
    attempt_number integer DEFAULT 1 NOT NULL,
    exercises_snapshot json,
    user_answers json,
    started_at timestamp(0) without time zone NOT NULL,
    completed_at timestamp(0) without time zone,
    is_completed boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lesson_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_attempts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_attempts_id_seq OWNED BY public.lesson_attempts.id;


--
-- Name: lesson_audio_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_audio_files (
    id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    audio_type character varying(255) NOT NULL,
    audio_key character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    duration integer,
    file_size integer,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    error_message text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lesson_audio_files_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'ready'::character varying, 'failed'::character varying])::text[])))
);


--
-- Name: lesson_audio_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_audio_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_audio_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_audio_files_id_seq OWNED BY public.lesson_audio_files.id;


--
-- Name: lesson_feedback_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_feedback_history (
    id bigint NOT NULL,
    lesson_history_id bigint,
    lesson_id bigint NOT NULL,
    user_id bigint NOT NULL,
    rating character varying(255) NOT NULL,
    comment text,
    original_feedback_id bigint,
    original_created_at timestamp(0) without time zone,
    archived_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT lesson_feedback_history_rating_check CHECK (((rating)::text = ANY ((ARRAY['like'::character varying, 'dislike'::character varying])::text[])))
);


--
-- Name: lesson_feedback_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_feedback_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_feedback_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_feedback_history_id_seq OWNED BY public.lesson_feedback_history.id;


--
-- Name: lesson_feedbacks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_feedbacks (
    id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    user_id bigint NOT NULL,
    rating character varying(255) NOT NULL,
    comment text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lesson_feedbacks_rating_check CHECK (((rating)::text = ANY ((ARRAY['like'::character varying, 'dislike'::character varying])::text[])))
);


--
-- Name: lesson_feedbacks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_feedbacks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_feedbacks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_feedbacks_id_seq OWNED BY public.lesson_feedbacks.id;


--
-- Name: lesson_histories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_histories (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    lesson_id bigint NOT NULL,
    user_id bigint NOT NULL,
    topic_id smallint NOT NULL,
    lesson_name character varying(255) NOT NULL,
    lesson_text text,
    language_level character varying(2) NOT NULL,
    lesson_lang character varying(255) NOT NULL,
    creation_type character varying(255) DEFAULT 'ai'::character varying NOT NULL,
    status character varying(255) DEFAULT 'archived'::character varying NOT NULL,
    grammar_data json,
    vocabulary json,
    dialogue json,
    exercises json,
    homework text,
    common_mistakes json,
    culture_notes text,
    embedding public.vector(1536),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lesson_histories_status_check CHECK (((status)::text = ANY ((ARRAY['created'::character varying, 'creation'::character varying, 'regeneration'::character varying, 'archived'::character varying, 'exists'::character varying, 'error'::character varying])::text[])))
);


--
-- Name: lesson_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_histories_id_seq OWNED BY public.lesson_histories.id;


--
-- Name: lesson_scores; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lesson_scores (
    id bigint NOT NULL,
    attempt_id bigint NOT NULL,
    lesson_id bigint NOT NULL,
    user_id bigint NOT NULL,
    teacher_id bigint,
    total_questions integer NOT NULL,
    correct_answers integer NOT NULL,
    score_percentage numeric(5,2) NOT NULL,
    auto_graded boolean DEFAULT true NOT NULL,
    teacher_comment text,
    graded_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lesson_scores_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lesson_scores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lesson_scores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lesson_scores_id_seq OWNED BY public.lesson_scores.id;


--
-- Name: lessons; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lessons (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    user_id bigint NOT NULL,
    topic_id smallint,
    lesson_name character varying(255) NOT NULL,
    lesson_text text,
    language_level character varying(2) NOT NULL,
    lesson_lang character varying(255) NOT NULL,
    creation_type character varying(255) DEFAULT 'ai'::character varying NOT NULL,
    status character varying(255) DEFAULT 'creation'::character varying NOT NULL,
    is_draft boolean DEFAULT false NOT NULL,
    grammar_data json,
    vocabulary json,
    dialogue json,
    exercises json,
    homework text,
    common_mistakes json,
    culture_notes text,
    embedding public.vector(1536),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT lessons_status_check CHECK (((status)::text = ANY ((ARRAY['created'::character varying, 'creation'::character varying, 'regeneration'::character varying, 'archived'::character varying, 'exists'::character varying, 'error'::character varying])::text[])))
);


--
-- Name: lessons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lessons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lessons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lessons_id_seq OWNED BY public.lessons.id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.messages (
    id bigint NOT NULL,
    chat_session_id bigint NOT NULL,
    user_id bigint NOT NULL,
    message text NOT NULL,
    is_from_admin boolean DEFAULT false NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: role_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_user (
    user_id bigint NOT NULL,
    role_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: teacher_invitations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.teacher_invitations (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    student_id bigint NOT NULL,
    message_id bigint,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: teacher_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.teacher_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teacher_invitations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.teacher_invitations_id_seq OWNED BY public.teacher_invitations.id;


--
-- Name: teacher_reviews; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.teacher_reviews (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    student_id bigint NOT NULL,
    rating smallint NOT NULL,
    comment text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: teacher_reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.teacher_reviews_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teacher_reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.teacher_reviews_id_seq OWNED BY public.teacher_reviews.id;


--
-- Name: teacher_student; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.teacher_student (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    student_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: teacher_student_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.teacher_student_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teacher_student_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.teacher_student_id_seq OWNED BY public.teacher_student.id;


--
-- Name: topic_translations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.topic_translations (
    id bigint NOT NULL,
    topic_id bigint NOT NULL,
    language_code character varying(5) NOT NULL,
    value character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: topic_translations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.topic_translations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: topic_translations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.topic_translations_id_seq OWNED BY public.topic_translations.id;


--
-- Name: topics; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.topics (
    id bigint NOT NULL,
    user_id bigint,
    slug character varying(255) NOT NULL,
    is_system boolean DEFAULT false NOT NULL,
    is_approved boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: topics_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.topics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: topics_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.topics_id_seq OWNED BY public.topics.id;


--
-- Name: user_blocks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_blocks (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    blocked_user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_blocks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_blocks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_blocks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_blocks_id_seq OWNED BY public.user_blocks.id;


--
-- Name: user_keyword_visibility; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_keyword_visibility (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    keyword_id bigint NOT NULL,
    is_hidden boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_keyword_visibility_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_keyword_visibility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_keyword_visibility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_keyword_visibility_id_seq OWNED BY public.user_keyword_visibility.id;


--
-- Name: user_learning_languages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_learning_languages (
    user_id bigint NOT NULL,
    language character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_messages (
    id bigint NOT NULL,
    sender_id bigint NOT NULL,
    recipient_id bigint NOT NULL,
    content text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    message_type character varying(20) DEFAULT 'text'::character varying NOT NULL
);


--
-- Name: user_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_messages_id_seq OWNED BY public.user_messages.id;


--
-- Name: user_reports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_reports (
    id bigint NOT NULL,
    reporter_id bigint NOT NULL,
    reported_user_id bigint NOT NULL,
    reason character varying(255) NOT NULL,
    description text,
    is_resolved boolean DEFAULT false NOT NULL,
    resolved_at timestamp(0) without time zone,
    resolved_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_reports_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_reports_id_seq OWNED BY public.user_reports.id;


--
-- Name: user_teaching_languages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_teaching_languages (
    user_id bigint NOT NULL,
    language character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_topic_visibility; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_topic_visibility (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    topic_id bigint NOT NULL,
    is_hidden boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_topic_visibility_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_topic_visibility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_topic_visibility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_topic_visibility_id_seq OWNED BY public.user_topic_visibility.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    first_login_completed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    zalo_user_id character varying(255),
    facebook_id character varying(255),
    avatar text,
    date_of_birth date NOT NULL,
    bio text,
    native_lang character varying(255) DEFAULT 'vi'::character varying NOT NULL,
    is_looking_for_teacher boolean DEFAULT false NOT NULL,
    is_accepting_students boolean DEFAULT false NOT NULL,
    settings json,
    timezone character varying(255) DEFAULT 'UTC'::character varying NOT NULL,
    is_temp_guest boolean DEFAULT false NOT NULL,
    temp_guest_expires_at timestamp(0) without time zone,
    is_banned boolean DEFAULT false NOT NULL,
    banned_at timestamp(0) without time zone,
    banned_by bigint,
    ban_reason character varying(255),
    ban_expires_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: chat_sessions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chat_sessions ALTER COLUMN id SET DEFAULT nextval('public.chat_sessions_id_seq'::regclass);


--
-- Name: course_assignments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments ALTER COLUMN id SET DEFAULT nextval('public.course_assignments_id_seq'::regclass);


--
-- Name: course_lessons id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_lessons ALTER COLUMN id SET DEFAULT nextval('public.course_lessons_id_seq'::regclass);


--
-- Name: course_progress id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress ALTER COLUMN id SET DEFAULT nextval('public.course_progress_id_seq'::regclass);


--
-- Name: courses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.courses ALTER COLUMN id SET DEFAULT nextval('public.courses_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: keyword_translations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keyword_translations ALTER COLUMN id SET DEFAULT nextval('public.keyword_translations_id_seq'::regclass);


--
-- Name: keywords id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords ALTER COLUMN id SET DEFAULT nextval('public.keywords_id_seq'::regclass);


--
-- Name: lesson_assignments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments ALTER COLUMN id SET DEFAULT nextval('public.lesson_assignments_id_seq'::regclass);


--
-- Name: lesson_attempts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_attempts ALTER COLUMN id SET DEFAULT nextval('public.lesson_attempts_id_seq'::regclass);


--
-- Name: lesson_audio_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_audio_files ALTER COLUMN id SET DEFAULT nextval('public.lesson_audio_files_id_seq'::regclass);


--
-- Name: lesson_feedback_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedback_history ALTER COLUMN id SET DEFAULT nextval('public.lesson_feedback_history_id_seq'::regclass);


--
-- Name: lesson_feedbacks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedbacks ALTER COLUMN id SET DEFAULT nextval('public.lesson_feedbacks_id_seq'::regclass);


--
-- Name: lesson_histories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_histories ALTER COLUMN id SET DEFAULT nextval('public.lesson_histories_id_seq'::regclass);


--
-- Name: lesson_scores id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores ALTER COLUMN id SET DEFAULT nextval('public.lesson_scores_id_seq'::regclass);


--
-- Name: lessons id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lessons ALTER COLUMN id SET DEFAULT nextval('public.lessons_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: teacher_invitations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_invitations ALTER COLUMN id SET DEFAULT nextval('public.teacher_invitations_id_seq'::regclass);


--
-- Name: teacher_reviews id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_reviews ALTER COLUMN id SET DEFAULT nextval('public.teacher_reviews_id_seq'::regclass);


--
-- Name: teacher_student id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_student ALTER COLUMN id SET DEFAULT nextval('public.teacher_student_id_seq'::regclass);


--
-- Name: topic_translations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topic_translations ALTER COLUMN id SET DEFAULT nextval('public.topic_translations_id_seq'::regclass);


--
-- Name: topics id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topics ALTER COLUMN id SET DEFAULT nextval('public.topics_id_seq'::regclass);


--
-- Name: user_blocks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_blocks ALTER COLUMN id SET DEFAULT nextval('public.user_blocks_id_seq'::regclass);


--
-- Name: user_keyword_visibility id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_keyword_visibility ALTER COLUMN id SET DEFAULT nextval('public.user_keyword_visibility_id_seq'::regclass);


--
-- Name: user_messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_messages ALTER COLUMN id SET DEFAULT nextval('public.user_messages_id_seq'::regclass);


--
-- Name: user_reports id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_reports ALTER COLUMN id SET DEFAULT nextval('public.user_reports_id_seq'::regclass);


--
-- Name: user_topic_visibility id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_topic_visibility ALTER COLUMN id SET DEFAULT nextval('public.user_topic_visibility_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: chat_sessions chat_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chat_sessions
    ADD CONSTRAINT chat_sessions_pkey PRIMARY KEY (id);


--
-- Name: course_assignments course_assignments_course_id_student_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments
    ADD CONSTRAINT course_assignments_course_id_student_id_unique UNIQUE (course_id, student_id);


--
-- Name: course_assignments course_assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments
    ADD CONSTRAINT course_assignments_pkey PRIMARY KEY (id);


--
-- Name: course_lessons course_lessons_course_id_lesson_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_lessons
    ADD CONSTRAINT course_lessons_course_id_lesson_id_unique UNIQUE (course_id, lesson_id);


--
-- Name: course_lessons course_lessons_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_lessons
    ADD CONSTRAINT course_lessons_pkey PRIMARY KEY (id);


--
-- Name: course_progress course_progress_course_id_user_id_lesson_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress
    ADD CONSTRAINT course_progress_course_id_user_id_lesson_id_unique UNIQUE (course_id, user_id, lesson_id);


--
-- Name: course_progress course_progress_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress
    ADD CONSTRAINT course_progress_pkey PRIMARY KEY (id);


--
-- Name: courses courses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.courses
    ADD CONSTRAINT courses_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: keyword_translations keyword_translations_keyword_id_language_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keyword_translations
    ADD CONSTRAINT keyword_translations_keyword_id_language_code_unique UNIQUE (keyword_id, language_code);


--
-- Name: keyword_translations keyword_translations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keyword_translations
    ADD CONSTRAINT keyword_translations_pkey PRIMARY KEY (id);


--
-- Name: keywords keywords_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords
    ADD CONSTRAINT keywords_pkey PRIMARY KEY (id);


--
-- Name: keywords keywords_topic_id_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords
    ADD CONSTRAINT keywords_topic_id_slug_unique UNIQUE (topic_id, slug);


--
-- Name: lesson_assignments lesson_assignments_lesson_id_student_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments
    ADD CONSTRAINT lesson_assignments_lesson_id_student_id_unique UNIQUE (lesson_id, student_id);


--
-- Name: lesson_assignments lesson_assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments
    ADD CONSTRAINT lesson_assignments_pkey PRIMARY KEY (id);


--
-- Name: lesson_attempts lesson_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_attempts
    ADD CONSTRAINT lesson_attempts_pkey PRIMARY KEY (id);


--
-- Name: lesson_audio_files lesson_audio_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_audio_files
    ADD CONSTRAINT lesson_audio_files_pkey PRIMARY KEY (id);


--
-- Name: lesson_feedback_history lesson_feedback_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedback_history
    ADD CONSTRAINT lesson_feedback_history_pkey PRIMARY KEY (id);


--
-- Name: lesson_feedbacks lesson_feedbacks_lesson_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedbacks
    ADD CONSTRAINT lesson_feedbacks_lesson_id_user_id_unique UNIQUE (lesson_id, user_id);


--
-- Name: lesson_feedbacks lesson_feedbacks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedbacks
    ADD CONSTRAINT lesson_feedbacks_pkey PRIMARY KEY (id);


--
-- Name: lesson_histories lesson_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_histories
    ADD CONSTRAINT lesson_histories_pkey PRIMARY KEY (id);


--
-- Name: lesson_scores lesson_scores_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores
    ADD CONSTRAINT lesson_scores_pkey PRIMARY KEY (id);


--
-- Name: lessons lessons_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lessons
    ADD CONSTRAINT lessons_pkey PRIMARY KEY (id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: role_user role_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_pkey PRIMARY KEY (user_id, role_id);


--
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: teacher_invitations teacher_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_invitations
    ADD CONSTRAINT teacher_invitations_pkey PRIMARY KEY (id);


--
-- Name: teacher_reviews teacher_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_reviews
    ADD CONSTRAINT teacher_reviews_pkey PRIMARY KEY (id);


--
-- Name: teacher_reviews teacher_reviews_teacher_id_student_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_reviews
    ADD CONSTRAINT teacher_reviews_teacher_id_student_id_unique UNIQUE (teacher_id, student_id);


--
-- Name: teacher_student teacher_student_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_student
    ADD CONSTRAINT teacher_student_pkey PRIMARY KEY (id);


--
-- Name: teacher_student teacher_student_teacher_id_student_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_student
    ADD CONSTRAINT teacher_student_teacher_id_student_id_unique UNIQUE (teacher_id, student_id);


--
-- Name: topic_translations topic_translations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topic_translations
    ADD CONSTRAINT topic_translations_pkey PRIMARY KEY (id);


--
-- Name: topic_translations topic_translations_topic_id_language_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topic_translations
    ADD CONSTRAINT topic_translations_topic_id_language_code_unique UNIQUE (topic_id, language_code);


--
-- Name: topics topics_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topics
    ADD CONSTRAINT topics_pkey PRIMARY KEY (id);


--
-- Name: topics topics_user_id_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topics
    ADD CONSTRAINT topics_user_id_slug_unique UNIQUE (user_id, slug);


--
-- Name: user_blocks user_blocks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_blocks
    ADD CONSTRAINT user_blocks_pkey PRIMARY KEY (id);


--
-- Name: user_blocks user_blocks_user_id_blocked_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_blocks
    ADD CONSTRAINT user_blocks_user_id_blocked_user_id_unique UNIQUE (user_id, blocked_user_id);


--
-- Name: user_keyword_visibility user_keyword_visibility_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_keyword_visibility
    ADD CONSTRAINT user_keyword_visibility_pkey PRIMARY KEY (id);


--
-- Name: user_keyword_visibility user_keyword_visibility_user_id_keyword_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_keyword_visibility
    ADD CONSTRAINT user_keyword_visibility_user_id_keyword_id_unique UNIQUE (user_id, keyword_id);


--
-- Name: user_learning_languages user_learning_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_learning_languages
    ADD CONSTRAINT user_learning_languages_pkey PRIMARY KEY (user_id, language);


--
-- Name: user_messages user_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_messages
    ADD CONSTRAINT user_messages_pkey PRIMARY KEY (id);


--
-- Name: user_reports user_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_reports
    ADD CONSTRAINT user_reports_pkey PRIMARY KEY (id);


--
-- Name: user_teaching_languages user_teaching_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_teaching_languages
    ADD CONSTRAINT user_teaching_languages_pkey PRIMARY KEY (user_id, language);


--
-- Name: user_topic_visibility user_topic_visibility_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_topic_visibility
    ADD CONSTRAINT user_topic_visibility_pkey PRIMARY KEY (id);


--
-- Name: user_topic_visibility user_topic_visibility_user_id_topic_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_topic_visibility
    ADD CONSTRAINT user_topic_visibility_user_id_topic_id_unique UNIQUE (user_id, topic_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_facebook_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_facebook_id_unique UNIQUE (facebook_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_uuid_unique UNIQUE (uuid);


--
-- Name: users users_zalo_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_zalo_user_id_unique UNIQUE (zalo_user_id);


--
-- Name: chat_sessions_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX chat_sessions_status_index ON public.chat_sessions USING btree (status);


--
-- Name: chat_sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX chat_sessions_user_id_index ON public.chat_sessions USING btree (user_id);


--
-- Name: course_assignments_student_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX course_assignments_student_id_status_index ON public.course_assignments USING btree (student_id, status);


--
-- Name: course_assignments_teacher_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX course_assignments_teacher_id_status_index ON public.course_assignments USING btree (teacher_id, status);


--
-- Name: course_lessons_course_id_order_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX course_lessons_course_id_order_index ON public.course_lessons USING btree (course_id, "order");


--
-- Name: course_progress_course_id_user_id_is_passed_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX course_progress_course_id_user_id_is_passed_index ON public.course_progress USING btree (course_id, user_id, is_passed);


--
-- Name: courses_user_id_is_published_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX courses_user_id_is_published_index ON public.courses USING btree (user_id, is_published);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: keyword_translations_language_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX keyword_translations_language_code_index ON public.keyword_translations USING btree (language_code);


--
-- Name: lesson_assignments_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_assignments_due_date_index ON public.lesson_assignments USING btree (due_date);


--
-- Name: lesson_assignments_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_assignments_status_index ON public.lesson_assignments USING btree (status);


--
-- Name: lesson_assignments_student_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_assignments_student_id_index ON public.lesson_assignments USING btree (student_id);


--
-- Name: lesson_assignments_teacher_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_assignments_teacher_id_index ON public.lesson_assignments USING btree (teacher_id);


--
-- Name: lesson_attempts_assignment_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_attempts_assignment_id_index ON public.lesson_attempts USING btree (assignment_id);


--
-- Name: lesson_attempts_completed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_attempts_completed_at_index ON public.lesson_attempts USING btree (completed_at);


--
-- Name: lesson_attempts_is_completed_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_attempts_is_completed_index ON public.lesson_attempts USING btree (is_completed);


--
-- Name: lesson_attempts_user_id_lesson_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_attempts_user_id_lesson_id_index ON public.lesson_attempts USING btree (user_id, lesson_id);


--
-- Name: lesson_audio_files_lesson_id_audio_type_audio_key_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_audio_files_lesson_id_audio_type_audio_key_index ON public.lesson_audio_files USING btree (lesson_id, audio_type, audio_key);


--
-- Name: lesson_audio_files_lesson_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_audio_files_lesson_id_status_index ON public.lesson_audio_files USING btree (lesson_id, status);


--
-- Name: lesson_embedding_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_embedding_idx ON public.lessons USING ivfflat (embedding public.vector_cosine_ops) WITH (lists='100');


--
-- Name: lesson_scores_attempt_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_scores_attempt_id_index ON public.lesson_scores USING btree (attempt_id);


--
-- Name: lesson_scores_graded_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_scores_graded_at_index ON public.lesson_scores USING btree (graded_at);


--
-- Name: lesson_scores_score_percentage_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_scores_score_percentage_index ON public.lesson_scores USING btree (score_percentage);


--
-- Name: lesson_scores_teacher_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_scores_teacher_id_index ON public.lesson_scores USING btree (teacher_id);


--
-- Name: lesson_scores_user_id_lesson_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lesson_scores_user_id_lesson_id_index ON public.lesson_scores USING btree (user_id, lesson_id);


--
-- Name: messages_chat_session_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX messages_chat_session_id_index ON public.messages USING btree (chat_session_id);


--
-- Name: messages_is_from_admin_is_read_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX messages_is_from_admin_is_read_index ON public.messages USING btree (is_from_admin, is_read);


--
-- Name: messages_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX messages_user_id_index ON public.messages USING btree (user_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: teacher_invitations_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_invitations_status_index ON public.teacher_invitations USING btree (status);


--
-- Name: teacher_invitations_student_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_invitations_student_id_index ON public.teacher_invitations USING btree (student_id);


--
-- Name: teacher_invitations_teacher_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_invitations_teacher_id_index ON public.teacher_invitations USING btree (teacher_id);


--
-- Name: teacher_reviews_teacher_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_reviews_teacher_id_index ON public.teacher_reviews USING btree (teacher_id);


--
-- Name: teacher_student_student_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_student_student_id_index ON public.teacher_student USING btree (student_id);


--
-- Name: teacher_student_teacher_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teacher_student_teacher_id_index ON public.teacher_student USING btree (teacher_id);


--
-- Name: topic_translations_language_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX topic_translations_language_code_index ON public.topic_translations USING btree (language_code);


--
-- Name: user_messages_recipient_id_sender_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_messages_recipient_id_sender_id_index ON public.user_messages USING btree (recipient_id, sender_id);


--
-- Name: user_messages_sender_id_recipient_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_messages_sender_id_recipient_id_index ON public.user_messages USING btree (sender_id, recipient_id);


--
-- Name: user_reports_reported_user_id_is_resolved_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_reports_reported_user_id_is_resolved_index ON public.user_reports USING btree (reported_user_id, is_resolved);


--
-- Name: users_is_temp_guest_temp_guest_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_is_temp_guest_temp_guest_expires_at_index ON public.users USING btree (is_temp_guest, temp_guest_expires_at);


--
-- Name: chat_sessions chat_sessions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chat_sessions
    ADD CONSTRAINT chat_sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: course_assignments course_assignments_course_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments
    ADD CONSTRAINT course_assignments_course_id_foreign FOREIGN KEY (course_id) REFERENCES public.courses(id) ON DELETE CASCADE;


--
-- Name: course_assignments course_assignments_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments
    ADD CONSTRAINT course_assignments_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: course_assignments course_assignments_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_assignments
    ADD CONSTRAINT course_assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: course_lessons course_lessons_course_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_lessons
    ADD CONSTRAINT course_lessons_course_id_foreign FOREIGN KEY (course_id) REFERENCES public.courses(id) ON DELETE CASCADE;


--
-- Name: course_lessons course_lessons_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_lessons
    ADD CONSTRAINT course_lessons_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: course_progress course_progress_course_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress
    ADD CONSTRAINT course_progress_course_id_foreign FOREIGN KEY (course_id) REFERENCES public.courses(id) ON DELETE CASCADE;


--
-- Name: course_progress course_progress_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress
    ADD CONSTRAINT course_progress_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: course_progress course_progress_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.course_progress
    ADD CONSTRAINT course_progress_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: courses courses_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.courses
    ADD CONSTRAINT courses_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: keyword_translations keyword_translations_keyword_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keyword_translations
    ADD CONSTRAINT keyword_translations_keyword_id_foreign FOREIGN KEY (keyword_id) REFERENCES public.keywords(id) ON DELETE CASCADE;


--
-- Name: keywords keywords_topic_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords
    ADD CONSTRAINT keywords_topic_id_foreign FOREIGN KEY (topic_id) REFERENCES public.topics(id) ON DELETE SET NULL;


--
-- Name: keywords keywords_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords
    ADD CONSTRAINT keywords_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_assignments lesson_assignments_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments
    ADD CONSTRAINT lesson_assignments_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_assignments lesson_assignments_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments
    ADD CONSTRAINT lesson_assignments_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_assignments lesson_assignments_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_assignments
    ADD CONSTRAINT lesson_assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_attempts lesson_attempts_assignment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_attempts
    ADD CONSTRAINT lesson_attempts_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES public.lesson_assignments(id) ON DELETE SET NULL;


--
-- Name: lesson_attempts lesson_attempts_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_attempts
    ADD CONSTRAINT lesson_attempts_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_attempts lesson_attempts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_attempts
    ADD CONSTRAINT lesson_attempts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_audio_files lesson_audio_files_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_audio_files
    ADD CONSTRAINT lesson_audio_files_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_feedback_history lesson_feedback_history_lesson_history_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedback_history
    ADD CONSTRAINT lesson_feedback_history_lesson_history_id_foreign FOREIGN KEY (lesson_history_id) REFERENCES public.lesson_histories(id) ON DELETE CASCADE;


--
-- Name: lesson_feedback_history lesson_feedback_history_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedback_history
    ADD CONSTRAINT lesson_feedback_history_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_feedback_history lesson_feedback_history_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedback_history
    ADD CONSTRAINT lesson_feedback_history_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_feedbacks lesson_feedbacks_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedbacks
    ADD CONSTRAINT lesson_feedbacks_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_feedbacks lesson_feedbacks_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_feedbacks
    ADD CONSTRAINT lesson_feedbacks_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_histories lesson_histories_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_histories
    ADD CONSTRAINT lesson_histories_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_histories lesson_histories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_histories
    ADD CONSTRAINT lesson_histories_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lesson_scores lesson_scores_attempt_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores
    ADD CONSTRAINT lesson_scores_attempt_id_foreign FOREIGN KEY (attempt_id) REFERENCES public.lesson_attempts(id) ON DELETE CASCADE;


--
-- Name: lesson_scores lesson_scores_lesson_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores
    ADD CONSTRAINT lesson_scores_lesson_id_foreign FOREIGN KEY (lesson_id) REFERENCES public.lessons(id) ON DELETE CASCADE;


--
-- Name: lesson_scores lesson_scores_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores
    ADD CONSTRAINT lesson_scores_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lesson_scores lesson_scores_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lesson_scores
    ADD CONSTRAINT lesson_scores_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lessons lessons_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lessons
    ADD CONSTRAINT lessons_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: messages messages_chat_session_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_chat_session_id_foreign FOREIGN KEY (chat_session_id) REFERENCES public.chat_sessions(id) ON DELETE CASCADE;


--
-- Name: messages messages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: role_user role_user_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: role_user role_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_invitations teacher_invitations_message_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_invitations
    ADD CONSTRAINT teacher_invitations_message_id_foreign FOREIGN KEY (message_id) REFERENCES public.user_messages(id) ON DELETE SET NULL;


--
-- Name: teacher_invitations teacher_invitations_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_invitations
    ADD CONSTRAINT teacher_invitations_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_invitations teacher_invitations_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_invitations
    ADD CONSTRAINT teacher_invitations_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_reviews teacher_reviews_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_reviews
    ADD CONSTRAINT teacher_reviews_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_reviews teacher_reviews_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_reviews
    ADD CONSTRAINT teacher_reviews_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_student teacher_student_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_student
    ADD CONSTRAINT teacher_student_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teacher_student teacher_student_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teacher_student
    ADD CONSTRAINT teacher_student_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: topic_translations topic_translations_topic_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topic_translations
    ADD CONSTRAINT topic_translations_topic_id_foreign FOREIGN KEY (topic_id) REFERENCES public.topics(id) ON DELETE CASCADE;


--
-- Name: topics topics_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.topics
    ADD CONSTRAINT topics_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_blocks user_blocks_blocked_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_blocks
    ADD CONSTRAINT user_blocks_blocked_user_id_foreign FOREIGN KEY (blocked_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_blocks user_blocks_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_blocks
    ADD CONSTRAINT user_blocks_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_keyword_visibility user_keyword_visibility_keyword_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_keyword_visibility
    ADD CONSTRAINT user_keyword_visibility_keyword_id_foreign FOREIGN KEY (keyword_id) REFERENCES public.keywords(id) ON DELETE CASCADE;


--
-- Name: user_keyword_visibility user_keyword_visibility_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_keyword_visibility
    ADD CONSTRAINT user_keyword_visibility_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_learning_languages user_learning_languages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_learning_languages
    ADD CONSTRAINT user_learning_languages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_messages user_messages_recipient_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_messages
    ADD CONSTRAINT user_messages_recipient_id_foreign FOREIGN KEY (recipient_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_messages user_messages_sender_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_messages
    ADD CONSTRAINT user_messages_sender_id_foreign FOREIGN KEY (sender_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_reports user_reports_reported_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_reports
    ADD CONSTRAINT user_reports_reported_user_id_foreign FOREIGN KEY (reported_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_reports user_reports_reporter_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_reports
    ADD CONSTRAINT user_reports_reporter_id_foreign FOREIGN KEY (reporter_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_reports user_reports_resolved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_reports
    ADD CONSTRAINT user_reports_resolved_by_foreign FOREIGN KEY (resolved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_teaching_languages user_teaching_languages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_teaching_languages
    ADD CONSTRAINT user_teaching_languages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_topic_visibility user_topic_visibility_topic_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_topic_visibility
    ADD CONSTRAINT user_topic_visibility_topic_id_foreign FOREIGN KEY (topic_id) REFERENCES public.topics(id) ON DELETE CASCADE;


--
-- Name: user_topic_visibility user_topic_visibility_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_topic_visibility
    ADD CONSTRAINT user_topic_visibility_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users users_banned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_banned_by_foreign FOREIGN KEY (banned_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict lzRRbi7kl34EPieJ2cKreuQEc1PLBMtPqx2jttfJE64feJx7qx0fHGQAEHYvcYA

