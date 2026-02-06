<x-app-layout>
    <h1 class="text-xl font-bold mb-4">Thêm từ vựng mới</h1>

    <form method="POST" action="/vocab">
        @csrf

        <div>
            <label>Từ tiếng Hàn</label><br>
            <input type="text" name="korean">
        </div>

        <div>
            <label>Nghĩa tiếng Việt</label><br>
            <input type="text" name="meaning">
        </div>

        <div>
            <label>Ví dụ</label><br>
            <textarea name="example"></textarea>
        </div>

        <button type="submit">Lưu</button>
    </form>
</x-app-layout>
