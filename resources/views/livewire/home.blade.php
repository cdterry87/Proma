<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h1 class="font-bold text-3xl">Dashboard</h1>
        <div>
            <x-inputs.select
                name="days"
                label="Select Timeframe"
                hide-label
            >
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="180">Last 180 days</option>
                <option value="365">Last 365 days</option>
            </x-inputs.select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stats shadow bg-base-200">

            <div class="stat">
                <div class="stat-title">Projects</div>
                <div class="stat-value">0</div>
                <div class="stat-desc">21% more than last month</div>
            </div>

        </div>
        <div class="stats shadow bg-base-200">

            <div class="stat">
                <div class="stat-title">Issues</div>
                <div class="stat-value">0</div>
                <div class="stat-desc">21% more than last month</div>
            </div>

        </div>
        <div class="stats shadow bg-base-200">

            <div class="stat">
                <div class="stat-title">Time Spent</div>
                <div class="stat-value">89,400</div>
                <div class="stat-desc">21% increase/decrease</div>
            </div>

        </div>
    </div>
</div>
