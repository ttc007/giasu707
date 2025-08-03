@if ($paginator->hasPages())
    <style type="text/css">
        .custom-pagination .page-link {
            border: none;
            margin: 0 4px;
            padding: 6px 12px;
            background-color: #f1f1f1;
            color: #333;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            min-width: 38px;
            text-align: center;
            border-radius: 6px;
        }

        .custom-pagination .page-link:hover {
            background-color: #dcedc8; /* Xanh chuối non */
            color: #fff;
        }

        .custom-pagination .page-item.active .page-link {
            background-color: #7cb342;
            color: #fff;
            font-weight: bold;
            box-shadow: 0 0 0 2px rgba(124, 179, 66, 0.4); /* Đổi shadow về cùng tone xanh */
        }

        .custom-pagination .page-item.disabled .page-link {
            background-color: #eee;
            color: #aaa;
            cursor: not-allowed;
        }

        @media (max-width: 576px) {
            .custom-pagination .page-link {
                padding: 4px 8px;
                font-size: 14px;
                margin: 0 2px;
            }
        }
    </style>
    <nav>
        <ul class="pagination justify-content-center my-4 custom-pagination">
            {{-- Nút "Trang trước" --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Các nút số trang --}}
            @foreach ($elements as $element)
                {{-- Dấu ... --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Các số trang --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Nút "Trang sau" --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
