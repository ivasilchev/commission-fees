
***Description*** 

This application provides a solution for the problem described in https://gist.github.com/mariusbalcytis/d997ddf15b4fdb21c37f785c4499c039

The intent was to abide to the following good practices : 

- Apply SOLID principles 
- DDD  
- Extensible with minimum effort 
- Applied design patterns 
- Good code coverage with unit tests
- Clear and intuitive design
- Minimum dependencies 

**Components** 
 
The application consists of a few "components" which try to achieve SoC as much as possible. 
Those should be easily replaced when deemed necessary with classes implementing the specific interface, 
transparently. 

- CommissionFeeApplication - The main application. Wires the autoloading, configuration and the services using manual constructor injection for dependencies.
- Parser - Used to parse the input data into fluent value objects 
- Processor - Used to process the data and prepare it for further consumption
- Repository - Data storage and retrieval 
- Strategy - Used to abstract the fee calculation logic 
- Resolver - Used to determine which fee calculation strategy to use for particular input data 
- ValueObject - Encapsulate data in immutable value objects 

**Decisions made** 

- Delegated currency conversion and handling to an external mature and well tested framework, as it seems a bit out of scope to handle internally. See: https://github.com/moneyphp/money
- 

**Requirements** 

- PHP 7.2^ 
- composer 

Other dependencies will be automatically installed with ```composer install```

**Installation** 

$ ```composer install``` 

**Running the application** 

$ ```php process.php <input file path>``` 

**Running tests**

$ ```vendor/bin/phpunit```

