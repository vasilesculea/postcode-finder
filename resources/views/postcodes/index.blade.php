<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PostCode Finder</title>

        <!-- Fonts -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <div class="container">
            <h1>Search by Postcode</h1>
            <form action="{{ route('api.postcodes.search') }}" method="get">
                <div class="form-group">
                    <label for="post-code">Postcode</label>

                    <input type="text"
                        id="post-code"
                        class="form-control"
                        name="postcode"
                        value="{{ old('postcode') }}"
                        placeholder="Postcode..."
                    />
                </div>

                <button type="submit" class="btn btn-primary">Search by Postcode</button>
            </form>

            <h1>Search by Position</h1>

            <form action="{{ route('api.postcodes.position') }}" method="get">
                <div class="form-group">
                    <label for="post-code">Lat</label>

                    <input type="text"
                        id="post-code"
                        class="form-control"
                        name="lat"
                        value="{{ old('lat') }}"
                        placeholder="Lat..."
                    />
                </div>

                <div class="form-group">
                    <label for="post-code">Long</label>

                    <input type="text"
                        id="post-code"
                        class="form-control"
                        name="lng"
                        value="{{ old('lng') }}"
                        placeholder="Long..."
                    />
                </div>

                <button type="submit" class="btn btn-primary">Search by Position</button>
            </form>
        </div>
    </body>
</html>
