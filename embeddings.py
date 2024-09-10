import os
import re
import requests
import sys
# from num2words import num2words
import os
import pandas as pd
import numpy as np
import tiktoken
from openai import AzureOpenAI

### Console configuration
pd.set_option('display.max_columns', None) # Set display settings in console to never shorten columns
pd.set_option('display.max_rows', None) # Set display settings in console to never shorten rows
pd.options.mode.chained_assignment = None #https://pandas.pydata.org/pandas-docs/stable/user_guide/indexing.html#evaluation-order-matters
###

### Globals
AZURE_API_KEY = "<insert azure api key here>"
DEPLOYMENT_ID = "<Embedding deployment ID>"
AZURE_ENDPOINT = "<Azure Endpoint>"
###

### Create Azure client
client = AzureOpenAI(
  api_key = AZURE_API_KEY,
  api_version = "2023-05-15",
  azure_endpoint = AZURE_ENDPOINT
)
###

def cosine_similarity(a, b):
    """
    This function determines similarily between two high dimensional vectors a and b.

    Parameters:
    a (List[float])     The first vector to be compared.
    b (List[float])     The second vector to be compared.

    Returns:
    float: A number between 0 and 1 denoting the closeness or similarity in vector space where 1 is exactly the same.
    """

    return np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b))

# Clean up text
def normalize_text(input):
    """
    This function cleans up empty spaces, newlines, and redundant characters that would get in the way of embeddings

    Parameters:
    input (string)      Input string to clean and normalize.

    Returns:
    string: The normalized string.
    """

    input = re.sub(r'\s+',  ' ', input).strip()
    input = re.sub(r". ,","",input)
    # remove all instances of multiple spaces
    input = input.replace("..",".")
    input = input.replace(". .",".")
    input = input.replace("\n", "")
    input = input.strip()

    return input

# Generate embeddings using Azure OpenAI API
def generate_embeddings(text, model=DEPLOYMENT_ID): # model = "deployment_name"
    """
    Generates embedding for the inputed text. Creates a many dimensional vector of the tokenized text that can be
    used to compare text with the cosine function.

    Parameters:
    text (string)       Text to generate an embedding for

    Returns:
    List[float]: Vector representation of tokenized text (embedding).
    """

    return client.embeddings.create(input = [text], model=model).data[0].embedding

def search_docs(df, user_query, top_n=4, to_print=True):
    """
    Creates an embedding for the user_query and compares to the MI resource document to pull out relevant sections.

    Parameters:
    df (DataFrame)      Data for the MI resource document
    user_query (string)     Query string of the user to be embedded and compared to the MI resource embedding
    to_print (bool)     Whether or not to print the search results to console

    Returns:
    DataFrame: Search results
    """

    embedding = generate_embeddings(
        user_query,
        model=DEPLOYMENT_ID # model should be set to the deployment name you chose when you deployed the text-embedding-ada-002 (Version 2) model
    )
    df["similarities"] = df.ada_v2.apply(lambda x: cosine_similarity(x, embedding))

    res = (
        df.sort_values("similarities", ascending=False)
        .head(top_n)
    )
    if to_print:
        print(res)
    return res

def find_highest_similarity(df):
    """
    Pick out the highest similarity result from the DataFrame

    Parameters:
    df (DataFrame)      Dataframe to pull results from

    Returns:
    DataFrame: Highest similarity index from inputed dataframe
    """

    return df.loc[df['similarities'].idxmax()]

# Read csv file into dataframe
df=pd.read_csv(os.path.join(os.getcwd(),'MITI document - Main.csv'))

# Remove unneeded columns
df = df[['text']]
df['text']= df["text"].apply(lambda x : normalize_text(x))

# Get rough number of tokens using separate package from what will be used in final result (tiktoken). Trim indices that are over token count
tokenizer = tiktoken.get_encoding("cl100k_base")
df['n_tokens'] = df["text"].apply(lambda x: len(tokenizer.encode(x)))
df = df[df.n_tokens<8192]

# Generate embeddings using AzureOpenAI
df['ada_v2'] = df["text"].apply(lambda x : generate_embeddings (x, model = DEPLOYMENT_ID)) # model should be set to the deployment name you chose when you deployed the text-embedding-ada-002 (Version 2) model

# Export embedded dataframe
print_df = df[['text', 'ada_v2']]
print_df.to_csv('miti_document_embedded.csv', index=False)

### Uncomment to use index search with command line argument
# # Use script argument for search phrase
# res = search_docs(df, sys.argv[1], top_n=4, to_print=False)
# res.to_csv('miti_document_embedded.csv', index=False)

# print(find_highest_similarity(res))