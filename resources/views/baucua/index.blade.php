@extends('layouts.app')

@section('title', 'Cờ tướng book')
@section('description', 'Cờ tướng book - Các khai cuộc thông dụng')
@section('keywords', 'Cờ tướng book - Luyện khai cuộc từ gà mờ đến cao thủ')

@section('content')
<style>
  /* Container bọc ngoài */
  .game-container {
    background: #f1f1f1;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    text-align: center;
    max-width: 600px;
  }

  /* Bàn cờ */
  canvas {
    border: 2px solid #333;
    border-radius: 8px;
    background-image: url("{{ asset('images/go2.png') }}");
    background-size: cover;
    background-position: center;
    display: block;
    margin: 0 auto;
  }

  /* Nhóm nút chung */
  .button-group {
    margin-bottom: 15px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }

  /* Nút icon */
  .button-group button {
    border: none;
    border-radius: 8px;
    background-color: #f8f9fa;
    cursor: pointer;
    padding: 10px;
    transition: background-color 0.3s, transform 0.1s;
  }

  .button-group button:hover {
    background-color: #e2e6ea;
  }

  .button-group button:active {
    transform: scale(0.95);
  }

  .button-group button img {
    width: 24px;
    height: 24px;
  }

  /* Nút Computer */
  .computer-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
  }

  #btnComputerGreen.active-green {
    background-color: #28a745;
  }

  #btnComputerRed.active-red {
    background-color: #dc3545;
  }

  .computer-btn.inactive {
    background-color: #9e9e9e !important;
  }

  .variation-btn {
    width: 200px;
    border: 1px solid #123 !important;
  }
</style>
<div class="text-center button-group">
    
    <div class="game-container mb-5">
        <div class="button-group">
            <label for="openingSelect" class="form-label d-block">Vui lòng chọn 1 thế trận để luyện tập</label>
            <select id="openingSelect" class="form-select" style="max-width: 300px;">
                <optgroup label="Đỏ">
                    @foreach(config('openings.red') as $opening)
                        <option value="{{ $opening['id'] }}" data-color="red">{{ $opening['name'] }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Xanh">
                    @foreach(config('openings.green') as $opening)
                        <option value="{{ $opening['id'] }}" data-color="green">{{ $opening['name'] }}</option>
                    @endforeach
                </optgroup>
            </select>

            
        </div>
        <div id="result" style="min-height:100px; text-align: left;" class="card p-2 mb-3 mx-3">
                
        </div>
        <canvas id="chessboard" width="420" height="500"></canvas>

        <div class="my-3 text-center" style='display:none'>
            <textarea id="comment" class="form-control" rows="3" style="max-width: 400px; margin: 0 auto;"></textarea>
        </div>

        <div class="button-group mt-4">
            <button id="newButton"><img src="{{ asset('images/new.png') }}" alt="New"></button>
            <button id="backButton"><img src="{{ asset('images/back.png') }}" alt="Back"></button>
            <button id="rotateButton"><img src="{{ asset('images/xoay.jpg') }}" alt="Rotate"></button>
            <button onclick='saveBook()' style="display:none"><img src="{{ asset('images/save.png') }}" alt="Save"></button>
        </div>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/book.js') }}"></script>
<script src="{{ asset('js/computerAI.js') }}"></script>
@endsection
