<style>
.support-page {
    color: #1f2933;
}
.support-page .support-header {
    align-items: center;
    border-bottom: 1px solid #e6eaf0;
    display: flex;
    gap: 12px;
    justify-content: space-between;
    margin-bottom: 14px;
    padding-bottom: 12px;
}
.support-page .support-title {
    align-items: center;
    display: flex;
    gap: 10px;
    margin: 0;
}
.support-page .support-title i {
    background: #eef6ff;
    border: 1px solid #cfe5ff;
    border-radius: 6px;
    color: #2176bd;
    height: 34px;
    line-height: 32px;
    text-align: center;
    width: 34px;
}
.support-page .support-panel {
    background: #fff;
    border: 1px solid #e6eaf0;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
    padding: 14px;
}
.support-page .support-toolbar {
    align-items: center;
    display: flex;
    gap: 10px;
    justify-content: space-between;
    margin: 12px 0;
}
.support-page .support-search {
    max-width: 360px;
    width: 100%;
}
.support-page .support-search .form-control {
    height: 38px;
}
.support-page .support-search .btn {
    height: 38px;
}
.support-page .support-tabs {
    border-bottom: 1px solid #d9e2ec;
    margin-bottom: 14px;
}
.support-page .support-tabs > li > a {
    border-radius: 6px 6px 0 0;
    color: #4b5563;
    font-weight: 600;
}
.support-page .support-tabs > li.active > a,
.support-page .support-tabs > li.active > a:focus,
.support-page .support-tabs > li.active > a:hover {
    color: #111827;
}
.support-page .support-table {
    margin-bottom: 0;
}
.support-page .support-table > thead > tr > th {
    background: #f8fafc;
    border-bottom: 1px solid #d9e2ec;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    vertical-align: middle;
}
.support-page .support-table > tbody > tr > td {
    border-top: 1px solid #edf2f7;
    vertical-align: middle;
}
.support-page .support-table > tbody > tr:hover {
    background: #fbfdff;
}
.support-page .support-actions {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.support-page .support-actions form {
    display: inline-block;
    margin: 0;
}
.support-page .btn {
    border-radius: 6px;
}
.support-page .btn-icon {
    align-items: center;
    display: inline-flex;
    gap: 6px;
}
.support-page .ticket-code {
    background: #eef2ff;
    border: 1px solid #d8defd;
    border-radius: 6px;
    color: #243b7a;
    display: inline-block;
    font-weight: 700;
    padding: 4px 8px;
}
.support-page .status-pill {
    border-radius: 999px;
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 10px;
}
.support-page .status-open {
    background: #e8f7ef;
    color: #17643a;
}
.support-page .status-pending {
    background: #fff7e6;
    color: #935c00;
}
.support-page .status-closed {
    background: #eef2f7;
    color: #475569;
}
.support-page .status-cancel {
    background: #fdecec;
    color: #a51d2d;
}
.support-page .text-truncate-soft {
    max-width: 280px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.support-page .empty-state {
    color: #64748b;
    padding: 26px 12px;
    text-align: center;
}
.support-page .empty-state i {
    color: #94a3b8;
    display: block;
    font-size: 24px;
    margin-bottom: 8px;
}
.support-page .support-pagination {
    margin: 14px 0 0;
}
.support-page .support-detail-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}
.support-page .support-detail-item {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 6px;
    padding: 10px 12px;
}
.support-page .support-detail-item.wide {
    grid-column: span 2;
}
.support-page .support-label {
    color: #64748b;
    display: block;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
    text-transform: uppercase;
}
.support-page .reply-thread {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.support-page .reply-item {
    border: 1px solid #e6eaf0;
    border-radius: 6px;
    padding: 12px;
}
.support-page .reply-item.admin {
    background: #f8fbff;
    border-color: #cfe5ff;
}
.support-page .reply-meta {
    align-items: center;
    color: #64748b;
    display: flex;
    font-size: 12px;
    justify-content: space-between;
    margin-bottom: 8px;
}
.support-page .reply-body {
    color: #1f2933;
    white-space: pre-wrap;
}
.support-page .attachment-thumb {
    border: 1px solid #e6eaf0;
    border-radius: 6px;
    display: block;
    margin-top: 8px;
    max-height: 130px;
    max-width: 180px;
    object-fit: cover;
}
.support-page .attachment-preview {
    max-width: 100%;
}
.support-page .content-area-footer {
    background: #fff;
    border-top: 1px solid #d9e2ec;
    box-shadow: 0 -2px 8px rgba(16, 24, 40, .08);
    padding: 12px 18px;
}
@media (max-width: 767px) {
    .support-page .support-header,
    .support-page .support-toolbar {
        align-items: stretch;
        flex-direction: column;
    }
    .support-page .support-search {
        max-width: none;
    }
    .support-page .support-actions {
        min-width: 150px;
    }
    .support-page .support-detail-grid {
        grid-template-columns: 1fr;
    }
    .support-page .support-detail-item.wide {
        grid-column: auto;
    }
}
</style>
